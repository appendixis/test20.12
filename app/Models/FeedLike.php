<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class FeedLike extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'feed_id', 'user_id',
    ];

    /**
     *
     */
    public function feed()
    {
        $this->belongsTo(Feed::class);
    }

    /**
     *
     */
    public function user()
    {
        $this->belongsTo(User::class);
    }
}
