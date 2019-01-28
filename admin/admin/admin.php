<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

\Bitrix\Main\Loader::includeModule('vendor.bitrixmodule');

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('VENDOR_PREFIX_PAGE_EDIT_TITLE'));

// html, js & php code

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");