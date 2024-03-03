<?php

/**
 * @package  Ubimmo
 */

namespace Ubi;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function getServices()
    {
        return [
            Base\Enqueue::class,
            Api\CptBiensController::class,
            Api\ImportController::class,
            Api\DashboardController::class,
            Api\CronController::class,
            Api\AnnonceursController::class,
            Api\MetaBoxesController::class,
            Api\SettingPageController::class,
            Api\ShortcodeController::class,

        ];
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     * @return
     */
    public static function registerServices()
    {
        foreach (self::getServices() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param  class $class    class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}
