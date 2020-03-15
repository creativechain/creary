<?php


namespace App\Utils;


use Illuminate\Database\Eloquent\Model;

class CreaContent extends Model
{

    protected $table = 'crea_content';

    public function setTags(array $tags) {
        $this->tags = json_encode($tags);
    }

    public function getTags() {
        return json_decode($this->tags);
    }
}
