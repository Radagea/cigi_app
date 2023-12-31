<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit29dabe336e5dddc1f52630daaf38f761
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Majframe\\' => 9,
        ),
        'C' => 
        array (
            'CigiApp\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Majframe\\' => 
        array (
            0 => __DIR__ . '/../..' . '/framework',
        ),
        'CigiApp\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit29dabe336e5dddc1f52630daaf38f761::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit29dabe336e5dddc1f52630daaf38f761::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit29dabe336e5dddc1f52630daaf38f761::$classMap;

        }, null, ClassLoader::class);
    }
}
