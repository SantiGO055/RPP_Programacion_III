<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4b27f27d3567a46bc5fad454ea28e144
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit4b27f27d3567a46bc5fad454ea28e144::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4b27f27d3567a46bc5fad454ea28e144::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
