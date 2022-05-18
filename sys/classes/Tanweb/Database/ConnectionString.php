<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Database;

use Tanweb\Container as Container;
use Error;

/**
 * Class creating connection strings for PDO, for now only mysql and postgres, 
 * update if necessary
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class ConnectionString {
    
    public static function create(Container $config) : string{
        $type = $config->getValue('type');
        switch($type){
            case 'mysql':
                return self::mysql($config);
            case 'postgres':
                return self::postgres($config);
            default:
                throw new Error('ConnectionStringFactory error: '
                        . 'type not defined.');
        }
    }
    
    private static function mysql(Container $config) : string {
        $host = $config->getValue('host');
        $name = $config->getValue('name');
        $charset = $config->getValue('charset');
        return 'mysql:host=' . $host . ';dbname=' . $name . ';charset=' . $charset;
    }
    
    private static function postgres(Container $config) : string {
        $host = $config->getValue('host');
        $port = $config->getValue('port');
        $name = $config->getValue('name');
        return 'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $name . ';';
    }
}
