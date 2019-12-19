<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

abstract class FeedEntity extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function feed()
    {
        return $this->morphOne(Feed::class, 'feedable');
    }
}
