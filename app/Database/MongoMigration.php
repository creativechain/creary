<?php


namespace App\Database;


use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

abstract class MongoMigration extends Migration
{

    protected $connection = 'mongodb';
}
