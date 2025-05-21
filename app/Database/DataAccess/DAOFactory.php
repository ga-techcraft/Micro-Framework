<?php

namespace Database\DataAccess;

use Database\DataAccess\Interfaces\ImagesDAO;
use Database\DataAccess\Interfaces\UserDAO;
use Helpers\Settings;

use Database\DataAccess\Implementions\ImagesDAOMySQLImpl;
use Database\DataAccess\Implementions\ImagesDAOMemcachedImpl;
use Database\DataAccess\Implementions\UserDAOImpl;

class DAOFactory{
    public static function getImagesDAO(): ImagesDAO{
        $driver = Settings::readEnvInfo('DATABASE_DRIVER');

        return match($driver){
            'mysql' => new ImagesDAOMySQLImpl(),
            'memcached' => new ImagesDAOMemcachedImpl(),
            default => throw new \Exception('Invalid database driver'),
        };

        // 上記は以下を簡略化したもの
        // if($driver === 'mysql'){
        //     return new ImagesDAOMySQLImpl();
        // } elseif($driver === 'memcached'){
        //     return new ImagesDAOMemcachedImpl();
        // }
        // throw new \Exception('Invalid database driver');
    }

    public static function getUserDAOImpl(): UserDAO{
        $driver = Settings::readEnvInfo('DATABASE_DRIVER');

        return match($driver){
            'mysql' => new UserDAOImpl(),
            default => throw new \Exception('Invalid database driver'),
        };
    }
}
