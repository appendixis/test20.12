<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    /**
     * @param Photo $photo
     * @return Photo
     */
    public function show(Photo $photo)
    {
        return $photo->feed;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png',
        ]);

        if ($request->hasFile('image')) {

            $photoItem = Photo::create([
                'path' => Storage::putFile('public/photos', $request->file('image')),
            ]);

            $photoItem->feed()->create([
                'user_id' => Auth::user()->id,
            ]);

            return $photoItem->feed;
        }
    }

    public function update(Request $request, Photo $photo)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png',
        ]);

        $this->authorize('edit', $photo->feed);

        if ($request->hasFile('image')) {
            $oldPath = $photo->path;

            $photo->feed->feedable->path = Storage::putFile('public/photos', $request->file('image'));
            $photo->feed->feedable->save();

            Storage::delete($oldPath);

            return $photo->feed;
        }
    }
}
