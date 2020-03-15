<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class AccountValidation extends Model
{

    protected $table = 'account_validation';

    protected $hidden = ['created_at', 'source', 'id'];

    /**
     * @return User|null
     */
    public function getUser() {
        return User::query()->find($this->user_id);
    }
}
