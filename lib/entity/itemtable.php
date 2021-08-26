<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

namespace Vendor\Bitrixmodule\Entity;

use Bitrix\Main\ORM\Fields;

/**
 * Class ItemTable
 * @package Vendor\Bitrixmodule
 */
class ItemTable extends \Vendor\Bitrixmodule\BaseTable
{
    /**
     * {@inheritdoc}
     */
    public static function getTableName(): string
    {
        return 'vendor_bitrixmodule_item';
    }

    /**
     * {@inheritdoc}
     */
    public static function getMap(): array
    {
        $map = [
            new Fields\StringField('NAME', ['required' => true]),
            new Fields\TextField('DESCRIPTION'),
        ];

        return array_merge(parent::getMap(), $map);
    }

    /**
     * {@inheritdoc}
     */
    public static function getIndexes(): array
    {
        return [];
    }
}