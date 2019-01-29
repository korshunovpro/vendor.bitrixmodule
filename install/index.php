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
    public function getPath($documentRoot = true)
    {
        $dirname = str_ireplace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__));
        if (!$documentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', $dirname);
        }
        return $dirname;
    }

    /**
     * @return array
     */
    public function getEntityClassList()
    {
        $result = [];
        $directory = new \DirectoryIterator($this->getPath() . '/lib/entity');
        /** @var \DirectoryIterator $entry */
        foreach ($directory as $entry) {
            if ($entry->getExtension() !== '.php') continue;
            $result[] = '\Vendor\Favorite\Entity\\' . ucfirst($entry->getBasename('.php') . 'Table');
        }
        return $result;
    }

    /**
     * @return void
     */
    public function DoInstall()
    {
        try {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallAgents();
        } catch (\Exception $exception) {
            $this->APPLICATION->ThrowException($exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function DoUninstall()
    {
        try {
            $this->UnInstallDB();
        } catch (\Exception $exception) {
            $this->APPLICATION->ThrowException($exception->getMessage());
        }

        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->UnInstallAgents();
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function InstallDB()
    {
        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
        foreach ($this->getEntityClassList() as $className) {
            /** @var \Vendor\Favorite\Entity $className */
            $entity = $className::getEntity();
            $connection = $entity->getConnection();
            $tableName = $entity->getDBTableName();

            if (!$connection->isTableExists($tableName)) {
                $entity->createDbTable();
                $className::createIndexes($connection);
            }
        }
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function UnInstallDB()
    {
        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
        foreach ($this->getEntityClassList() as $className) {
            /** @var \Vendor\Favorite\Entity $className */
            $entity = $className::getEntity();
            $connection = $entity->getConnection();
            $tableName = $entity->getDBTableName();

            if ($connection->isTableExists($tableName)) {
                $connection->dropTable($tableName);
            }
        }
    }

    /**
     * @return void
     */
    public function InstallFiles()
    {
        //install admin files
        $directory = new \DirectoryIterator($this->getPath() . '/admin/admin');
        /** @var \DirectoryIterator $entry */
        foreach ($directory as $entry) {
            if ($entry->getExtension() !== '.php') continue;
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . strtolower(__CLASS__) . '_' . $entry->getFilename(),
                '<?php' . ' require(\'' . $this->getPath() . '/admin/admin/' . $entry->getFilename() . '\');' . ' ?>'
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
        $directory = new \DirectoryIterator($this->getPath() . '/admin/admin');
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