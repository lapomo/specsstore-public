<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6a5ae03253a00fb783937f726b9113c4
{
    public static $files = array (
        'ecaf24f0c429cf706d11f02c1a2d9a13' => __DIR__ . '/../..' . '/includes/helper-functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WishSuite\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WishSuite\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6a5ae03253a00fb783937f726b9113c4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6a5ae03253a00fb783937f726b9113c4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6a5ae03253a00fb783937f726b9113c4::$classMap;

        }, null, ClassLoader::class);
    }
}
