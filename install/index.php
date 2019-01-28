<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class vendor_bitrixmodule
 */
class vendor_bitrixmodule extends CModule
{
    /**
     * @var CAllMain|CMain|null
     */
    private $APPLICATION = null;

    public function __construct()
    {
        global $APPLICATION;
        $this->APPLICATION = $APPLICATION;

        $arModuleVersion = [];
        require __DIR__ . '/version.php';

        $this->MODULE_ID = str_replace('_', '.', __CLASS__);
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('VENDOR_PREFIX_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('VENDOR_PREFIX_MODULE_DESC');

        $this->PARTNER_NAME = Loc::getMessage('VENDOR_PREFIX_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('VENDOR_PREFIX_PARTNER_URI');

        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    /**
     *
     */
    public function DoInstall()
    {
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     *
     */
    public function DoUninstall()
    {
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool
     */
    public function InstallDB()
    {
        return false;
    }

    /**
     * @return bool|void
     */
    public function UnInstallDB()
    {
        return true;
    }

    /**
     * @return bool|void
     */
    public function InstallFiles()
    {
        return true;
    }

    /**
     * @return bool|void
     */
    public function UnInstallFiles()
    {
        return true;
    }

    /**
     * @return bool|void
     */
    public function InstallEvents()
    {
        return true;
    }

    /**
     * @return bool|void
     */
    public function UnInstallEvents()
    {
        return true;
    }
}