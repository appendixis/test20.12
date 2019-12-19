<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class Photo extends FeedEntity
{
    /**
     * @var array
     */
    protected $fillable = [
        'path',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'url',
    ];

    /**
     * @return mixed
     */
    public function getUrlAttribute()
    {
        return $this->attributes['url'] = Storage::url($this->attributes['path']);;
    }

}
