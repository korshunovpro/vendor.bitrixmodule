<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

namespace Vendor\Bitrixmodule;

use Bitrix\Main\ORM\Fields;

/**
 * Class Item
 * @package Vendor\Bitrixmodule
 */
class ItemTable extends Entity
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'vendor_bitrixmodule_item';
    }

    /**
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        $map = [
            // entity data
            new Fields\StringField('NAME', ['required' => true]),
            new Fields\TextField('DESCRIPTION'),
        ];

        return array_merge(parent::getMap(), $map);
    }
}