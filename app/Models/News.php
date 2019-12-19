<?php

namespace App\Models;

class News extends FeedEntity
{
    /**
     * @var string
     */
    protected $table = 'news';

    /**
     * @var array
     */
    protected $fillable = [
        'text',
    ];

}
