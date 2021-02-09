<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model;

class Op extends Model
{

    protected $connection = 'mongodb';
    protected $dates = ['timestamp'];
    protected $fillable = ['type', 'timestamp'];

    public static function register($op, $timestamp) {
        self::query()
            ->create([
                'type' => $op[0],
                'timestamp' => $timestamp
            ]);
    }
}
