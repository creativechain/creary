<?php
/**
 * Created by PhpStorm.
 * User: salman
 * Date: 2/22/19
 * Time: 1:29 PM
 */

return [

    'host'     => env('MQTT_HOST','127.0.0.1'),
    'password' => env('MQTT_ADMIN_PASSWORD',''),
    'username' => env('MQTT_ADMIN_USER',''),
    'certfile' => env('MQTT_KEY_FILE',''),
    'port'     => env('MQTT_PORT','1883'),
    'debug'    => env('mqtt_debug',false), //Optional Parameter to enable debugging set it to True
    'qos'      => env('mqtt_qos', 0), // set quality of service here
    'retain'   => env('mqtt_retain', 0) // it should be 0 or 1 Whether the message should be retained.- Retain Flag
];
