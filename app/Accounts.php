<?php


namespace App;


use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

class Accounts extends Model
{
    protected $connection = 'mongodb';
    protected $primaryKey = 'id';

    /**
     * @param array $data
     * @return $this
     */
    public function applyData($data) {
        $dataCollection = collect($data);

        $accountData = $dataCollection->only(['id', 'name', 'post_count', 'follower_count', 'following_count']);

        $keys = $accountData->keys();
        foreach ($keys as $k) {
            $this->$k = $accountData->get($k);
        }

        $metadata = array();
        try {
            $metadata = $dataCollection->get('json_metadata');
            $metadata = json_decode($metadata, true);
            $this->public_name = null;
            if (array_key_exists('publicName', $metadata)) {
                $this->public_name = $metadata['publicName'];
            }
        } catch (\Exception $e) {
            Log::error('Cannot get metadata for account ' . $this->name, $e->getTrace());
        }

        //dd($metadata, $this);
        $this->metadata = $metadata;

        return $this;
    }
}
