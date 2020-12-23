<?php


namespace App;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

class Comments extends Model
{

    protected $connection = 'mongodb';
    protected $fillable = ['cashout_at', 'created_at', 'updated_at'];
    protected $dates = ['cashout_at', 'created_at', 'updated_at'];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->timestamps = false;
    }

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
        $this->author = $data->author->name;
        $this->title = $data->title;
        $this->reblogged_by = $data->reblogged_by;

        $aR = $data->total_payout_value;
        $aR = (intval($aR->amount) / pow(10, $aR->precision));
        $cR = $data->curator_payout_value;
        $cR = (intval($cR->amount) / pow(10, $cR->precision));

        $this->author_reward = $aR;
        $this->curator_reward = $cR;

        $this->reward = $aR + $cR;

        $this->cashout_at = Carbon::parse($data->cashout_time);
        $this->created_at = Carbon::parse($data->created);
        $this->updated_at = Carbon::parse($data->last_update);

        $this->is_paid = Carbon::now()->isAfter($this->cashout_at);
        $this->save();

        //Download
        $this->download = null;
        if ($data->download->size > 0) {
            //Comment has download
            $dAmount = intval($data->download->price->amount);
            $this->download = ($dAmount > 0 ? 'paid' : 'free');
        }

        try {
            $data->metadata = json_decode($data->json_metadata, true);
            $this->description = $data->metadata['description'];
            $this->license = $data->metadata['license'];
            $this->adult = $data->metadata['adult'];
            $tags = $data->metadata['tags'];
            $this->save();
            foreach ($tags as $t) {
                /** @var Tags $mT */
                $mT = Tags::query()
                    ->firstOrCreate([
                        'name' => $t
                    ], [
                        'name' => $t
                    ]);

                $mT->comments()->attach($this->_id);
                $mT->setCommentsCount();
                $mT->save();
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' Comment: ' . $data->author . '/' . $data->permlink, $e->getTrace());
        }

        return $this;
    }
}
