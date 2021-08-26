<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

/**
 * Class vendor_bitrixmodule
 */
class vendor_bitrixmodule extends Vendor\Bitrixmodule\Module
{
    /**
     * {@inheritdoc}.
     */
    public function getName(): string
    {
        return Loc::getMessage('VENDOR_BITRIXMODULE_MODULE_NAME');
    }

    /**
     * {@inheritdoc}.
     */
    public function getDescription(): string
    {
        return Loc::getMessage('VENDOR_BITRIXMODULE_MODULE_DESC');
    }

    /**
     * {@inheritdoc}.
     */
    public function getPartnerName(): string
    {
        return Loc::getMessage('VENDOR_BITRIXMODULE_PARTNER_NAME');
    }

    /**
     * {@inheritdoc}.
     */
    public function getPartnerUri(): string
    {
        return Loc::getMessage('VENDOR_BITRIXMODULE_PARTNER_URI');
    }

    /**
     * {@inheritdoc}.
     */
    public function getDir(): string
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleTableClasses(): array
    {
        return [
            \Vendor\Bitrixmodule\Entity\ItemTable::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleFiles(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleEvents(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleAgents(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleTasks(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function doInstall()
    {
        parent::doInstall();

        foreach ($this->getModuleTableClasses() as $className) {
            /** @var BaseTable $className */
            $entity = $className::getEntity();
            $connection = $entity->getConnection();

            if (method_exists($className, 'afterCreateTable')) {
                $className::afterCreateTable();
            }

            foreach ($className::getIndexes() as $k => $columns) {
                $connection->createIndex(
                    $entity->getDBTableName(),
                    'IX_' . $entity->getDBTableName() . '_' . $k,
                    $columns
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function doUninstall()
    {
        $this->APPLICATION->ThrowException(
            Loc::getMessage(
                'ERROR_UNINSTALL_DISABLED',
                [
                    '#MODULE#' => $this->MODULE_ID,
                ]
            )
        );

        return false;
    }

    /**
     * Деинсталляция, кастомный метод для прямого вызова.
     */
    public function doUninstallInternal()
    {
        parent::doUninstall();
    }
}