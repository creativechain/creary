<?php


namespace App;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Contents extends Model
{

    protected $primaryKey = ['permlink', 'author'];
    public $incrementing = false;

    protected $casts = [
        'tags' => 'array',
        'reblogged_by' => 'array',
    ];

    public function applyData($post) {
        $this->permlink = $post->permlink;
        $this->author = $post->author->name;
        $this->title = $post->title;
        $this->description = $post->metadata->description;
        $this->license = $post->metadata->license;
        $this->tags = $post->metadata->tags;
        $this->adult = $post->metadata->adult;
        $this->created_at = Carbon::parse($post->created);

        $this->save();
    }

    public function addReblog($user) {
        if (!$this->reblogged_by) {
            $this->reblogged_by = array($user);
        } else {

            $reblogs = $this->reblogged_by;
            if (!in_array($user, $reblogs)) {
                $reblogs[] = $user;
                $this->reblogged_by = $reblogs;
            }

        }
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
