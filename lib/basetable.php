<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

namespace Vendor\Bitrixmodule;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Type;

/**
 * Class Entity
 * @package Vendor\Bitrixmodule
 */
/**
 * Базовый класс сущности *Table.
 * Реализует базовую структуру полей таблицы:
 * ID, TIMESTAMP_X(onUpdate), DATE_CREATE.
 *
 * Class BaseTable.
 */
abstract class BaseTable extends DataManager
{
    /**
     * Название таблицы в БД.
     */
    abstract public static function getTableName(): string;

    /**
     * Базовый список полей сущности, ID, дата создания, дата изменения.
     *
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap(): array
    {
        return [
            // primary
            new Fields\IntegerField('ID', [
                'primary' => true,
                'unique' => true,
                'autocomplete' => true,
            ]),
            // date
            new Fields\DatetimeField('TIMESTAMP_X', ['default_value' => new Type\DateTime()]),
            new Fields\DatetimeField('DATE_CREATE', ['default_value' => new Type\DateTime()]),
        ];
    }

    /**
     * Обработчик события добавления элемента,
     * обновляет поле TIMESTAMP_X текщая метка времени.
     *
     * @param Event $event Объект события
     *
     * @throws \Bitrix\Main\ObjectException
     */
    public static function onBeforeUpdate(Event $event): EventResult
    {
        $result = new EventResult();
        $result->modifyFields(['TIMESTAMP_X' => new Type\DateTime()]);

        return $result;
    }

    /**
     * Список индексов для БД (объявляется в наследнике).
     *
     * Должен возвращать массив полей или массив массивов полей
     * по которым нужно создать индекс в БД или.
     */
    abstract public static function getIndexes(): array;
}