<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use App\Models\FeedLike;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return Feed::orderBy('created_at', 'desc')->paginate();
    }

    /**
     * @param Feed $feed
     * @return Feed
     */
    public function show(Feed $feed)
    {
        return $feed;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
        ]);

        if ($request->input('type') === 'news') {
            return (new NewsController)->store($request);
        }
        if ($request->input('type') === 'photo') {
            return (new PhotoController)->store($request);
        }
    }

    /**
     * @param Request $request
     * @param Feed $feed
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Feed $feed)
    {
        $request->validate([
            'type' => 'required',
        ]);

        $this->authorize('edit', $feed);

        if ($request->input('type') === 'news') {
            return (new NewsController)->update($request, $feed->feedable);
        }
        if ($request->input('type') === 'photo') {
            return (new PhotoController)->update($request, $feed->feedable);
        }
    }

    /**
     * @param Feed $feed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Feed $feed)
    {
        $this->authorize('edit', $feed);

        if ($feed->feedable_type === 'photo') {
            Storage::delete($feed->feedable->path);
        }

        $feed->feedable->delete();
        $feed->delete();
    }

    /**
     * @param Feed $feed
     * @return Feed
     */
    public function like(Feed $feed)
    {
        if ($feed->has_like) {
            $feed->likes()->where('user_id', Auth::user()->id)->delete();
        } else {
            $like = new FeedLike([
                'user_id' => Auth::user()->id,
            ]);
            $feed->likes()->save($like);
        }

        return Feed::findOrFail($feed->id);
    }

}
