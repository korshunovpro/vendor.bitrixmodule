<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

\Bitrix\Main\Loader::includeModule('vendor.bitrixmodule');

use Vendor\Bitrixmodule\Module;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Menu in admin area
 */
global $APPLICATION;
if ($APPLICATION::GetGroupRight(Module::getModuleId()) > 'D') {
    $arMenu = [
        'parent_menu' => 'global_menu_services',
        'section' => Module::getModulePrefix(),
        'sort' => 900,
        'text' => Loc::getMessage('VENDOR_PREFIX_MENU_MODULE_TITLE'),
        'title' => Loc::getMessage('VENDOR_PREFIX_MENU_MODULE_TEXT'),
        'icon' => Module::getModulePrefix() . '-icon',
        'items_id' => 'menu_' . Module::getModulePrefix(),
        'items' => [
            [
                'text' => Loc::getMessage('VENDOR_PREFIX_MENU_PAGE_EDIT_TEXT'),
                'url' => Module::getModulePrefix() . '_edit.php?lang=' . LANGUAGE_ID,
                'more_url' => [Module::getModulePrefix() . '_edit.php'],
                'title' => Loc::getMessage('VENDOR_PREFIX_MENU_PAGE_EDIT_TITLE'),
            ]
        ]
    ];
    return $arMenu;
}