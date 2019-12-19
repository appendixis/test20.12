<?php
namespace App\Policies;

use App\Models\Feed;
use App\User;

class FeedPolicy
{
    /**
     * @param User $user
     * @param Feed $feed
     * @return bool
     */
    public function edit(User $user, Feed $feed)
    {
        return $user->id === $feed->user_id;
    }
}
