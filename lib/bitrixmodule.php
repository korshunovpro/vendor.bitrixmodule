<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

namespace Vendor\Bitrixmodule;

/**
 * Class BitrixModule
 */
class BitrixModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getDir(): string
    {
        return __DIR__;
    }
}