<?php


namespace App;


use App\Events\TagsUpdatingEvent;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

class Tags extends Model
{

    protected $connection = 'mongodb';
    protected $fillable = ['name'];
    protected $hidden = ['comments_ids'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Jenssegers\Mongodb\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Comments::class);
    }

    public function setCommentsCount()
    {
        $this->comments_count = count($this->comments_ids);
    }

}
