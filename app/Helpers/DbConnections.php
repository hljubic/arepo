<?php


namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DbConnections
{
    public static function setConnection($params)
    {
        config(['database.connections.onthefly' => [
            'driver' => $params->driver,
            'host' => $params->host,
            'database' => $params->database,
            'username' => $params->username,
            'password' => $params->password
        ]]);

        return DB::connection('onthefly');
    }

    public static function setStrixConnection(string $database)
    {
        config(['database.connections.strix' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => 5433,
            'database' => $database,
            'username' => 'web',
            'password' => env('STRIX_DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer'
        ]]);

        return DB::connection('strix');
    }
}
