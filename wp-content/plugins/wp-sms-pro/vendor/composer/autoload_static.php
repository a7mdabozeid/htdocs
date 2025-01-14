<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfe3c4208616120779398fc287f939b22
{
    public static $files = array (
        '49a1299791c25c6fd83542c6fedacddd' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v4p11.php',
    );

    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'apimatic\\jsonmapper\\' => 20,
        ),
        'V' => 
        array (
            'VeronaLabs\\Updater\\' => 19,
        ),
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
        'M' => 
        array (
            'MessageBird\\' => 12,
        ),
        'C' => 
        array (
            'ClickSendLib\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'apimatic\\jsonmapper\\' => 
        array (
            0 => __DIR__ . '/..' . '/apimatic/jsonmapper/src',
        ),
        'VeronaLabs\\Updater\\' => 
        array (
            0 => __DIR__ . '/..' . '/veronalabs/updater/src',
        ),
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/src/Twilio',
        ),
        'MessageBird\\' => 
        array (
            0 => __DIR__ . '/..' . '/messagebird/php-rest-api/src/MessageBird',
        ),
        'ClickSendLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/clicksend/clicksend-php/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'U' => 
        array (
            'Unirest\\' => 
            array (
                0 => __DIR__ . '/..' . '/mashape/unirest-php/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfe3c4208616120779398fc287f939b22::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfe3c4208616120779398fc287f939b22::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitfe3c4208616120779398fc287f939b22::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
