<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

namespace Vendor\Bitrixmodule;

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Data\DataManager;
use CModule;
use Throwable;

/**
 * Абстрактный базовый класс модуля Битрикс реализующий конструктор битрикс модуля
 * и методы инсталляции|деинсталляции.
 *
 * abstract Module.
 */
abstract class Module extends CModule
{
    /**
     * @var null|\CAllMain|\CMain
     */
    protected $APPLICATION;

    /**
     * Название модуля.
     */
    abstract public function getName(): string;

    /**
     * Должен возвращать значение константы __DIR__.
     */
    abstract public function getDir(): string;

    /**
     * Конструктор, стандартная инициализация Битрикс модуля.
     * Устанавливается название, описание, данные партнера, версия.
     *
     * Module constructor.
     */
    public function __construct()
    {
        global $APPLICATION;

        $this->APPLICATION = $APPLICATION;

        $this->MODULE_ID = str_replace('_', '.', static::class);

        $this->MODULE_NAME = $this->getName();
        $this->MODULE_DESCRIPTION = $this->getDescription();

        $this->PARTNER_NAME = $this->getPartnerName();
        $this->PARTNER_URI = $this->getPartnerUri();

        $this->initVersion();
        $this->initRights();
    }

    /**
     * ID модуля.
     */
    public function getModuleId(): string
    {
        return $this->MODULE_ID;
    }

    /**
     * Метод установки модуля.
     */
    public function doInstall()
    {
        try {
            if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
                ModuleManager::registerModule($this->MODULE_ID);
            }
            Loader::includeModule($this->MODULE_ID);
            $this->installDB();
            $this->installFiles();
            $this->installEvents();
            $this->installAgents();
        } catch (Throwable $exception) {
            $this->APPLICATION->ThrowException($exception->getMessage());
        }
    }

    /**
     * Метод удаления модуля.
     */
    public function doUninstall()
    {
        try {
            $this->uninstallDB();
            $this->uninstallFiles();
            $this->uninstallEvents();
            $this->uninstallAgents();
        } catch (Throwable $exception) {
            $this->APPLICATION->ThrowException($exception->getMessage());
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * Создание таблиц БД по классам *Table.
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function installDB()
    {
        /** @var DataManager $class */
        foreach ($this->getModuleTableClasses() as $class) {
            $entity = $class::getEntity();
            $connection = $entity->getConnection();
            if (!$connection->isTableExists($entity->getDBTableName())) {
                $entity->createDbTable();
            }

            // Проверка кастомного метода. Метод с действиями после создания таблицы.
            if (method_exists($class, 'afterCreateDbTable')) {
                $class::afterCreateDbTable();
            }

            // Проверка кастомного метода. Метод должен возвращать массив полей для индекса.
            if (method_exists($class, 'getIndexes') && is_array($class::getIndexes())) {
                foreach ($class::getIndexes() as $k => $columns) {
                    $connection->createIndex(
                        $entity->getDBTableName(),
                        'IX_' . $entity->getDBTableName() . '_' . $k,
                        $columns
                    );
                }
            }
        }
    }

    /**
     * Удаление таблиц БД по классам *Table.
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function uninstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        /** @var DataManager $class */
        foreach ($this->getModuleTableClasses() as $class) {
            $entity = $class::getEntity();
            $connection = $entity->getConnection();
            if ($connection->isTableExists($entity->getDBTableName())) {
                $connection->dropTable($entity->getDBTableName());
            }
        }
    }

    /**
     * Регистрация обработчиков событий.
     */
    public function installEvents()
    {
        foreach ($this->getModuleEvents() as $event) {
            EventManager::getInstance()->registerEventHandler(
                $event['fromModuleId'],
                $event['eventType'],
                $event['toModuleId'] ?? '',
                $event['toClass'] ?? '',
                $event['toMethod'] ?? '',
                $event['sort'] ?? 100,
                $event['toPath'] ?? '',
                $event['toMethodArg'] ?? []
            );
        }
    }

    /**
     * Удаление зарегистрированных обработчиков событий.
     */
    public function uninstallEvents()
    {
        foreach ($this->getModuleEvents() as $event) {
            EventManager::getInstance()->unRegisterEventHandler(
                $event['fromModuleId'],
                $event['eventType'],
                $event['toModuleId'] ?? '',
                $event['toClass'] ?? '',
                $event['toMethod'] ?? '',
                $event['toPath'] ?? '',
                $event['toMethodArg'] ?? []
            );
        }
    }

    /**
     * Регистрация агентов.
     */
    public function installAgents()
    {
        foreach ($this->getModuleAgents() as $agent) {
            $now = date('d.m.Y H:i:s');
            \CAgent::AddAgent(
                $agent['name'],
                $agent['module'] ?? '',
                $agent['period'] ?? 'N',
                $agent['interval'] ?? 86400,
                $agent['datecheck'] ?? $now,
                $agent['active'] ?? 'Y',
                $agent['next_exec'] ?? $now,
                $agent['sort'] ?? 30,
                $agent['user_id'] ?? false,
                $agent['existError'] ?? true
            );
        }
    }

    /**
     * Удаление агентов.
     */
    public function uninstallAgents()
    {
        foreach ($this->getModuleAgents() as $agent) {
            \CAgent::RemoveAgent(
                $agent['name'],
                $agent['module'] ?? '',
                $agent['user_id'] ?? false
            );
        }
    }

    /**
     * Установка файлов и директорий.
     */
    public function installFiles()
    {
        foreach ($this->getModuleFiles() as $from => $to) {
            if (is_dir($from) && IO\Directory::isDirectoryExists($from)) {
                CopyDirFiles($from, $to, true, true);
            } elseif (IO\File::isFileExists($from)) {
                IO\File::putFileContents($to, IO\File::getFileContents($from));
            }
        }
    }

    /**
     * Удаление файлов и директорий.
     */
    public function uninstallFiles()
    {
        foreach ($this->getModuleFiles() as $from => $to) {
            if (is_dir($to) && IO\Directory::isDirectoryExists($to)) {
                IO\Directory::deleteDirectory($to);
            } elseif (IO\File::isFileExists($to)) {
                IO\File::deleteFile($to);
            }
        }
    }

    /**
     * Абсолютный путь к модулю.
     */
    public function getPath(): string
    {
        return str_ireplace(DIRECTORY_SEPARATOR, '/', dirname($this->getDir()));
    }

    /**
     * Относительный путь к модулю.
     */
    public function getRelativePath(): string
    {
        return str_ireplace(Application::getDocumentRoot(), '', $this->getPath());
    }

    /**
     * Подключение файла version.php и инициализация MODULE_VERSION, MODULE_VERSION_DATE.
     */
    protected function initVersion(): void
    {
        $arModuleVersion = [];
        require $this->getDir() . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    /**
     * Установки контроля прав.
     */
    protected function initRights(): void
    {
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    /**
     * Описание модуля.
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * Название партнера/разработчика модуля.
     */
    public function getPartnerName(): string
    {
        return '';
    }

    /**
     * Ссылка на сайт партнера/разработчика модуля.
     */
    public function getPartnerUri(): string
    {
        return '';
    }

    /**
     * Массив *Table классов модуля (может быть пустой).
     *
     * Пример:
     *
     * return [
     *      'ModuleNamespace\SubNamespace\ClassNameFooTable',
     *      'ModuleNamespace\SubNamespace\ClassNameBarTable',
     * ]
     */
    public function getModuleTableClasses(): array
    {
        return [];
    }

    /**
     * Массив файлов/директорий для установки (может быть пустой).
     *
     * Пример:
     *
     * return [
     *      'full_path/install/admin/edit.php' => 'full_path/bitrix/admin/edit.php,
     *      'full_path/install/admin/manage.php' => 'full_path/bitrix/admin/manage.php,
     *      'full_path/install/js' => 'full_path/bitrix/js/module_id,
     * ]
     */
    public function getModuleFiles(): array
    {
        return [];
    }

    /**
     * Массив событий для установки (может быть пустой).
     *
     * Пример:
     *
     * return [
     *      [
     *          'fromModuleId' => 'main',
     *          'eventType' => 'OnPageStart',
     *          'toModuleId' => 'module_id',
     *          'toClass' => 'Class',
     *          'toMethod' => 'Method',
     *          'sort' => '100',
     *          'toPath' => '', // Путь к файлу который надо подключить относительно /local или /bitrix
     *          'toMethodArg' => [$param1, $param2],
     *      ], [
     *          'fromModuleId' => 'main',
     *          'eventType' => 'OnEpilog',
     *          'toModuleId' => 'module_id',
     *          'toClass' => 'Class',
     *          'toMethod' => 'Method',
     *          'sort' => '100000',
     *          'toPath' => '', // Путь к файлу который надо подключить относительно /local или /bitrix
     *          'toMethodArg' => [$param1, $param2],
     *      ]
     * ]
     */
    public function getModuleEvents(): array
    {
        return [];
    }

    /**
     * Массив агентов для установки (может быть пустой).
     *
     * Пример:
     *
     * return [
     *      [
     *           'name' => 'Class::method();',          // строка, Функция агента
     *           'module' => 'module_id',               // default = ''
     *           'period' => '' ,                       // Y/N, default = N
     *           'interval' => '',                      // sec, default = 86400, интервал запуска
     *           'datecheck' => 'dd.mm.yyyy hh:mm:ss',  // default = now(), дата первой проверки на запуск
     *           'active' => '',                        // Y/N, default = Y, активность
     *           'next_exec' => 'dd.mm.yyyy hh:mm:ss',   // default = now(), дата первого запуска
     *           'sort' => '',                          // default = 30, Сортировка
     *           'user_id' => '',                       // default = false, пользователь
     *           'existError' => '',                    // default = true, возвращать ошибку если агент существует
     *     ], [
     *           'name' => 'Class::method();',
     *           'module' => 'module_id',
     *           'period' => '' ,
     *           'interval' => '',
     *           'datecheck' => 'dd.mm.yyyy hh:mm:ss',
     *           'active' => '',
     *           'next_exec' => 'dd.mm.yyyy hh:mm:ss',
     *           'sort' => '',
     *           'user_id' => '',
     *           'existError' => '',
     *     ]
     * ]
     */
    public function getModuleAgents(): array
    {
        return [];
    }

    /**
     * Массив заданий для пользователя (может быть пустой).
     *
     * Пример:
     *
     * return [
     *     [
     *         'NAME' => [
     *             'LETTER' => '',
     *             'BINDING' => '',
     *             'OPERATIONS' => [
     *                 'NAME',
     *                 'NAME',
     *             ],
     *         ],
     *     ], [
     *         'NAME' => [
     *             'LETTER' => '',
     *             'BINDING' => '',
     *             'OPERATIONS' => [
     *                 'NAME',
     *                 'NAME',
     *             ],
     *         ],
     *     ]
     * ]
     */
    public function getModuleTasks()
    {
        return [];
    }
}
