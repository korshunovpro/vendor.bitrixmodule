<?php

namespace Vendor\Bitrixmodule;

/**
 * Interface ModuleInterface.
 */
interface ModuleInterface
{
    /**
     * ID модуля.
     */
    public function getModuleId(): string;

    /**
     * Название модуля.
     */
    public function getName(): string;

    /**
     * Описание модуля.
     */
    public function getDescription(): string;

    /**
     * Название партнера/разработчика модуля.
     */
    public function getPartnerName(): string;

    /**
     * Ссылка на сайт партнера/разработчика модуля.
     */
    public function getPartnerUri(): string;

    /**
     * Должен возвращать значение константы __DIR__.
     */
    public function getDir(): string;

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
    public function getModuleTableClasses(): array;

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
    public function getModuleFiles(): array;

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
    public function getModuleEvents(): array;

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
    public function getModuleAgents(): array;

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
    public function getModuleTasks(): array;
}
