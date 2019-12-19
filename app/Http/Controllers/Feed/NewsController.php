<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{

    /**
     * @param News $news
     * @return News
     */
    public function show(News $news)
    {
        return $news->feed;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required',
        ]);

        $newsItem = News::create([
            'text' => $request->input('text'),
        ]);

        $newsItem->feed()->create([
            'user_id' => Auth::user()->id,
        ]);

        return $newsItem->feed;
    }

    /**
     * @param Request $request
     * @param News $news
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'text' => 'required',
        ]);

        $this->authorize('edit', $news->feed);

        $news->feed->feedable->text = $request->input('text');
        $news->feed->feedable->save();

        return $news->feed;
    }
}
