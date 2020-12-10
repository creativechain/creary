<?php


namespace App;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

class Comments extends Model
{

    protected $connection = 'mongodb';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Jenssegers\Mongodb\Relations\BelongsToMany
     */
    public function tags() {
        return $this->belongsToMany(Tags::class);
    }

    /**
     * @param $data
     * @return $this
     */
    public function applyData($data) {
        $this->permlink = $data->permlink;
        $this->author = $data->author;
        $this->title = $data->title;

        $this->created_at = Carbon::parse($data->timestamp);

        $this->save();

        try {
            $data->metadata = json_decode($data->json_metadata, true);
            $this->description = $data->metadata['description'];
            $this->license = $data->metadata['license'];
            $this->adult = $data->metadata['adult'];
            $rTags = $this->tags();
            $tags = $data->metadata['tags'];
            foreach ($tags as $t) {
                $mT = Tags::query()
                    ->firstOrCreate([
                        'name' => $t
                    ], [
                        'name' => $t
                    ]);

                $rTags->attach($mT->_id);
            }

            $this->save();

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' Comment: ' . $data->author . '/' . $data->permlink, $e->getTrace());
        }

        return $this;
    }
}
