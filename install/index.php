<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

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
     * @param bool $documentRoot
     * @return mixed
     */
    public function GetPath($documentRoot = true)
    {
        $dirname = str_ireplace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__));
        if (!$documentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', $dirname);
        }
        return $dirname;
    }

    /**
     *
     */
    public function DoInstall()
    {
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallEvents();
    }

    /**
     *
     */
    public function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
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
        //install admin files
        $directory = new \DirectoryIterator($this->GetPath() . '/admin/admin');
        /** @var \DirectoryIterator $entry */
        foreach ($directory as $entry) {
            if ($entry->getExtension() !== '.php') continue;
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . strtolower(__CLASS__) . '_' . $entry->getFilename(),
                '<?php' . ' require(\'' . $this->GetPath() . '/admin/admin/' . $entry->getFilename() . '\');' . ' ?>'
            );
        }

        // install other files
        CopyDirFiles(__DIR__ . '/css', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/' . $this->MODULE_ID . '/', true, true);
        CopyDirFiles(__DIR__ . '/js', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . $this->MODULE_ID . '/', true, true);
        CopyDirFiles(__DIR__ . '/images', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/' . $this->MODULE_ID . '/', true, true);
    }

    /**
     * @return bool|void
     */
    public function UnInstallFiles()
    {
        // uninstall admin files
        $directory = new \DirectoryIterator($this->GetPath() . '/admin/admin');
        /** @var \DirectoryIterator $entry */
        foreach ($directory as $entry) {
            if ($entry->isDot() && $entry->getExtension() !== '.php') continue;
            \Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . strtolower(__CLASS__) . '_' . $entry->getFilename());
        }

        // uninstall other files
        DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/');
        DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');
        DeleteDirFilesEx('/bitrix/images/' . $this->MODULE_ID . '/');
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

    /**
     * @return bool|void
     */
    public function InstallAgents()
    {
        return true;
    }

    /**
     * @return bool|void
     */
    public function UnInstallAgents()
    {
        return true;
    }
}