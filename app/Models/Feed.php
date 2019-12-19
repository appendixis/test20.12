<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Feed extends Model
{
    /**
     * @var string
     */
    protected $table = "feed";

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'entity_type', 'entity_id',
    ];

    /**
     * @var array
     */
    protected $with = [
        'user', 'feedable',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'like_counter', 'has_like',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'likes',
    ];

    /**
     * @return mixed
     */
    public function getLikeCounterAttribute()
    {
        return $this->attributes['like_counter'] = $this->likes->count();
    }

    /**
     * @return bool
     */
    public function getHasLikeAttribute()
    {
        return Auth::user() && $this->attributes['has_like'] = $this->likes->where('user_id', Auth::user()->id)->count() > 0;
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function feedable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(FeedLike::class);
    }
}
