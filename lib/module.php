<?php
/**
 * Bitrix module skeleton
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @license	https://opensource.org/licenses/MIT	MIT License
 */

/**
 * Class Module
 */
class Module
{
    /**
     * @return array
     */
    public static function getPathArray()
    {
        return explode(DIRECTORY_SEPARATOR , dirname(__DIR__));
    }

    /**
     * Module ID by folder name
     *
     * @return mixed
     */
    public static function getModuleId()
    {
        $path = self::getPathArray();
        return $path[count($path)-1];
    }

    /**
     * @return mixed
     */
    public static function getModulePrefix()
    {
        return str_ireplace('.', '_', self::getModuleId());
    }
}