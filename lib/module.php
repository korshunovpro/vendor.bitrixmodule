<?php

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
 * Абстрактный базовый класс модуля Битрикс с методами инсталляции деинсталляции.
 *
 * abstract Module.
 */
abstract class Module extends CModule implements ModuleInterface
{
    /**
     * @var null|\CAllMain|\CMain
     */
    protected $APPLICATION;

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
     * {@inheritdoc}
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

            // Метод с действиями после создания таблицы.
            if (method_exists($class, 'afterCreateTable')) {
                $class::afterCreateTable();
            }

            // Метод должен возвращать массив полей для индекса.
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
}
