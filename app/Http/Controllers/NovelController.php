<?php

namespace App\Http\Controllers;

use App\Models\Novel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class NovelController extends Controller
{

    public function home() {
        $datas = Novel::inRandomOrder()->get();
        $recommends = Novel::with('recommend')->has('recommend')->get();
        $stars = Novel::orderBy('stars', 'desc')->take(20)->get();
        $newbooks = Novel::orderBy('created_at', 'desc')->take(20)->get();
        $topreaders = Novel::orderBy('readed', 'desc')->take(20)->get();
        $carousels = \App\Models\Carousel::orderBy('id', 'desc')->get();
        return Inertia::render('Home', [
            'datas' => compact('datas'), 
            'recommends' => compact('recommends'), 
            'stars' => compact('stars'), 
            'newbooks' => compact('newbooks'), 
            'topreaders' => compact('topreaders'),
            'carousels' => $carousels
        ]);
    }

    public function detail($id) {
        $data = Novel::with('author')->with('genres')->with('categories')->with('comments.user')->with([
            'volumes' => function($query) {
                $query->orderBy('no');
            },
            'volumes.chapters' => function($query) {
                $query->orderBy('no');
            }
        ])->where('id', $id)->first();
        if(!$data) {
            abort(404);
        }

        // Check if logged-in user has liked/starred this novel
        $userId = Auth::id();
        $isLiked   = $userId ? $data->likedByUsers()->where('user_id', $userId)->exists() : false;
        $isStarred = $userId ? $data->starredByUsers()->where('user_id', $userId)->exists() : false;

        return Inertia::render('NovelDetail', [
            'data'      => compact('data'),
            'isLiked'   => $isLiked,
            'isStarred' => $isStarred,
        ]);
    }

    public function content(Request $request, $id) {
        $volume = $request->query('volume');
        $chapter = $request->query('chapter');
        $data = Novel::with('author')->with([
            'volumes' => function($query) use ($volume) {
                $query->where('no', $volume);
            },
            'volumes.chapters' => function($query) use ($chapter) {
                $query->where('no', $chapter);
            },
            'volumes.chapters.content'
        ])->where('id', $id)->first();
        if(!$data) {
            abort(404);
        }
        return Inertia::render('NovelContent', ['data' => compact('data')]);
    }

    public function post_comment(Request $request) {
        Comment::create($request->all());
    }

    public function post_novel(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author_id'   => 'required|exists:authors,id',
            'description' => 'nullable|string',
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $validated['cover'] = $path;
        }

        Novel::create($validated);

        return redirect()->route('author.dashboard')->with('message', 'Novel berhasil dibuat!');
    }

    // ─── Like toggle ───────────────────────────────────────
    public function toggleLike($id)
    {
        $user  = Auth::user();
        $novel = Novel::findOrFail($id);

        $exists = $novel->likedByUsers()->where('user_id', $user->id)->exists();

        if ($exists) {
            $novel->likedByUsers()->detach($user->id);
            $liked = false;
        } else {
            $novel->likedByUsers()->attach($user->id);
            $liked = true;
        }

        return back()->with(['liked' => $liked]);
    }

    // ─── Star toggle ───────────────────────────────────────
    public function toggleStar($id)
    {
        $user  = Auth::user();
        $novel = Novel::findOrFail($id);

        $exists = $novel->starredByUsers()->where('user_id', $user->id)->exists();

        if ($exists) {
            $novel->starredByUsers()->detach($user->id);
            // Decrement global star count (min 0)
            $novel->decrement('stars');
            $starred = false;
        } else {
            $novel->starredByUsers()->attach($user->id);
            $novel->increment('stars');
            $starred = true;
        }

        return back()->with(['starred' => $starred]);
    }

    // ─── Increment read count ──────────────────────────────
    public function incrementRead($id)
    {
        $novel = Novel::findOrFail($id);
        $novel->increment('readed');
        return response()->json(['readed' => $novel->readed]);
    }

    // ─── Increment download count ──────────────────────────
    public function incrementDownload($id)
    {
        $novel = Novel::findOrFail($id);
        $novel->increment('downloaded');
        return response()->json(['downloaded' => $novel->downloaded]);
    }
}
