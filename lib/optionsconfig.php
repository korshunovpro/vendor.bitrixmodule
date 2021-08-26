<?php

namespace Vendor\Bitrixmodule;

use Bitrix\Main\Localization\Loc;

/**
 * Класс описания настроек модуля.
 *
 * Class OptionsConfig.
 */
class OptionsConfig extends Options\OptionsConfig
{
    // Опция ID интернов
    public const MAIN_OPTION = 'MAIN_OPTION';

    /**
     * Массив объявления настроек.
     */
    public static function getOptions(): array
    {
        return [
            'main_options' => [
                'TAB' => Loc::getMessage('VENDOR_BITRIXMODULE_OPTION_MAIN_TAB_TITLE'),
                'TITLE' => Loc::getMessage('VENDOR_BITRIXMODULE_OPTION_MAIN_TAB_TITLE_TITLE'),
                'OPTIONS' => [
                    self::MAIN_OPTION => [
                        'LABEL' => Loc::getMessage('VENDOR_BITRIXMODULE_OPTIONS_TASK_MUST_NOT_CONTAIN'),
                        'VALUE' => 'robot',
                        'TYPE' => ['text', 10],
                    ],
                ],
            ],
        ];
    }
}
