<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

namespace Vendor\Bitrixmodule;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Type;

/**
 * Class Entity
 * @package Vendor\Bitrixmodule
 */
abstract class Entity extends DataManager
{
    /**
     * @return array
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            // primary
            new Fields\IntegerField('ID', [
                'primary' => true,
                'unique' => true,
                'autocomplete' => true
            ]),
            // date data
            new Fields\DatetimeField('TIMESTAMP_X', ['default_value' => new Type\DateTime()]),
            new Fields\DatetimeField('DATE_CREATE', ['default_value' => new Type\DateTime()]),
        ];
    }

    /**
     * @param \Bitrix\Main\ORM\Event $event
     * @return EventResult|void
     * @throws \Bitrix\Main\ObjectException
     */
    public static function onBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $result = new EventResult();
        $result->modifyFields(['TIMESTAMP_X' => new Type\DateTime()]);
        return $result;
    }

    /**
     * @return array
     */
    public static function getIndexes()
    {
        return [];
    }

    /**
     * @param \Bitrix\Main\DB\Connection $connection
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function createIndexes(\Bitrix\Main\DB\Connection $connection)
    {
        $tableName = static::getTableName();
        foreach (static::getIndexes() as $k => $columns) {
            $connection->createIndex($tableName, 'IX_' . $tableName . '_' . $k, $columns);
        }
    }
}