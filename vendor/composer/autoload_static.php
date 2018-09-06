<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit590f3a3c88d6d18eaa2296faa60f4a1f
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit590f3a3c88d6d18eaa2296faa60f4a1f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit590f3a3c88d6d18eaa2296faa60f4a1f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
