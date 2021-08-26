<?php

/**
 * Страница настроек.
 */

use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Vendor\Bitrixmodule\OptionsConfig;
use Vendor\Bitrixmodule\Options\OptionsForm;

$path = explode(DIRECTORY_SEPARATOR, __DIR__);
$module_id = end($path);

Loader::includeModule($module_id);
Loc::loadMessages(__FILE__);

OptionsForm::execute(
    $module_id,
    new OptionsConfig(),
    HttpApplication::getInstance()->getContext()->getRequest()
);
