<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model;

class Tags extends Model
{

    protected $connection = 'mongodb';
    protected $fillable = ['name'];
    protected $appends = ['comments_count'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Jenssegers\Mongodb\Relations\BelongsToMany
     */
    public function comments() {
        return $this->belongsToMany(Comments::class);
    }

    /**
     * @return int
     */
    public function getCommentsCountAttribute() {
        return $this->comments()->count();
    }
}
