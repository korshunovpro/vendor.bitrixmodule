<?php

namespace Vendor\Bitrixmodule\Options;

use Bitrix\Main\Localization\Loc;

/**
 * Класс описания настроек модуля.
 *
 * Class OptionsConfig.
 */
abstract class OptionsConfig
{
    /**
     * Массив объявления настроек.
     */
    abstract public static function getOptions(): array;
}
