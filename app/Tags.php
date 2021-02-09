<?php


namespace App;


use App\Events\TagsUpdatingEvent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

class Tags extends Model
{

    protected $connection = 'mongodb';
    protected $fillable = ['name'];
    protected $hidden = ['comments_ids', '_id', 'updated_at', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Jenssegers\Mongodb\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Comments::class);
    }

    /**
     * @return Collection
     */
    public function activeComments(): Collection
    {
        return $this->comments()
            ->where('is_paid', false)
            ->get();
    }

    public function setCommentsCount()
    {
        $this->comments_count = count($this->comments_ids);
    }

    public function setActiveCommentsCount() {
        $this->active_comments = $this->activeComments()->count();
    }

    public function updateCounters() {
        $this->setCommentsCount();
        $this->setActiveCommentsCount();
    }

}
