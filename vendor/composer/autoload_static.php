<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteda5fb6c04b225ff03508083ae8760c3
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'C' => 
        array (
            'Core\\' => 5,
        ),
        'B' => 
        array (
            'Base\\' => 5,
        ),
        'A' => 
        array (
            'Api\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Core',
        ),
        'Base\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Base',
        ),
        'Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Api',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticIniteda5fb6c04b225ff03508083ae8760c3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticIniteda5fb6c04b225ff03508083ae8760c3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticIniteda5fb6c04b225ff03508083ae8760c3::$classMap;

        }, null, ClassLoader::class);
    }
}