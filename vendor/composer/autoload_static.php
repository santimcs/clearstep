<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7e89bb60f7e84bf98b7caa640971d2bf
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'D' => 
        array (
            'Davaxi\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'Davaxi\\' => 
        array (
            0 => __DIR__ . '/..' . '/davaxi/sparkline/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7e89bb60f7e84bf98b7caa640971d2bf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7e89bb60f7e84bf98b7caa640971d2bf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7e89bb60f7e84bf98b7caa640971d2bf::$classMap;

        }, null, ClassLoader::class);
    }
}