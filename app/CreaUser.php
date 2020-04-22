<?php


namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CreaUser extends Model
{
    use Notifiable;

    protected $table = 'crea_users';

    protected $fillable = [ 'name' ];

    public function routeNotificationFor($driver, $notification = null)
    {
        return $this->notifications();
    }
}
