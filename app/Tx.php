<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model;

class Tx extends Model
{

    protected $connection = 'mongodb';
    protected $dates = ['timestamp'];
    protected $fillable = ['hash', 'timestamp'];

    public static function register($tx, $timestamp) {
        self::query()
            ->create([
                'hash' => $tx->transaction_id,
                'timestamp' => $timestamp
            ]);
    }
}
