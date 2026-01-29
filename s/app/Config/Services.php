<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\HTTP\SiteURIFactory;
use CodeIgniter\Superglobals;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * The Factory for SiteURI.
     *
     * @return SiteURIFactory
     */
    public static function siteurifactory(
        ?App $config = null,
        ?Superglobals $superglobals = null,
        bool $getShared = true,
    ) {
        if ($getShared) {
            return static::getSharedInstance('siteurifactory', $config, $superglobals);
        }

        // Fix for "Argument #1 must be Config\App, null given" error
        if ($config === null) {
            $config = config('App');
        }

        if ($config === null && class_exists(App::class)) {
            $config = new App();
        }

        $superglobals ??= static::get('superglobals');

        return new SiteURIFactory($config, $superglobals);
    }

    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */
}
