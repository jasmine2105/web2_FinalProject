<?php

class ComposerAutoloaderInit0ff862f5fac033a25b7c1fe3c8e9d877
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit0ff862f5fac033a25b7c1fe3c8e9d877', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit0ff862f5fac033a25b7c1fe3c8e9d877', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit0ff862f5fac033a25b7c1fe3c8e9d877::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
