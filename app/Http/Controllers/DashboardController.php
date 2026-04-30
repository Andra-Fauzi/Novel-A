<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Novel;
use App\Models\Author;
use App\Models\User;
use App\Models\Volume;
use App\Models\Chapter;
use App\Models\Content;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    // ─────────────────────────────────────────────
    // USER (generic)
    // ─────────────────────────────────────────────
    public function user_dashboard()
    {
        $user = Auth::user();

        $likedNovels   = $user ? $user->likedNovels()->with('author')->get() : collect();
        $starredNovels = $user ? $user->starredNovels()->with('author')->get() : collect();

        return Inertia::render('Dashboard', [
            'likedNovels'   => $likedNovels,
            'starredNovels' => $starredNovels,
        ]);
    }

    // ─────────────────────────────────────────────
    // AUTHOR
    // ─────────────────────────────────────────────
    public function author_dashboard()
    {
        $author = Author::with([
            'novels' => function ($q) {
                $q->withCount(['volumes', 'comments']);
            },
            'novels.volumes' => function ($q) {
                $q->withCount('chapters');
            },
        ])->find(Auth::id());

        return Inertia::render('author/Dashboard', [
            'authorData' => $author,
        ]);
    }

    /** Author: update their own novel */
    public function author_update_novel(Request $request, $id)
    {
        $novel = Novel::where('id', $id)
            ->where('author_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status'      => 'sometimes|in:Ongoing,Completed,Hiatus',
            'cover'       => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            if ($novel->cover) Storage::disk('public')->delete($novel->cover);
            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $novel->update($validated);

        return redirect()->route('author.dashboard')->with('message', 'Novel updated!');
    }

    /** Author: delete their own novel */
    public function author_delete_novel($id)
    {
        $novel = Novel::where('id', $id)
            ->where('author_id', Auth::id())
            ->firstOrFail();

        if ($novel->cover) Storage::disk('public')->delete($novel->cover);
        $novel->delete();

        return redirect()->route('author.dashboard')->with('message', 'Novel deleted!');
    }

    // ─────────────────────────────────────────────
    // ADD VOLUMES / CHAPTERS
    // ─────────────────────────────────────────────
    public function addVolumesOrChapters($id)
    {
        $novel = Novel::with([
            'volumes' => function ($q) {
                $q->orderBy('no');
            },
            'volumes.chapters' => function ($q) {
                $q->orderBy('no');
            },
            'volumes.chapters.content',
        ])->where('id', $id)
          ->where('author_id', Auth::id())
          ->firstOrFail();

        return Inertia::render('author/AddChaptersOrVolume', [
            'novel' => $novel,
        ]);
    }

    /** Store a new volume */
    public function storeVolume(Request $request, $novelId)
    {
        $novel = Novel::where('id', $novelId)
            ->where('author_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'no' => 'required|integer|min:1',
        ]);

        Volume::create([
            'novel_id'       => $novel->id,
            'no'             => $request->no,
            'chapters_total' => 0,
        ]);

        $novel->increment('volumes_total');

        return redirect()->route('addVolumesOrChapters', $novelId)->with('message', 'Volume added!');
    }

    /** Store a new chapter (+ content) */
    public function storeChapter(Request $request, $volumeId)
    {
        $volume = Volume::with('novel')->findOrFail($volumeId);

        // Ensure the author owns this novel
        abort_unless($volume->novel->author_id === Auth::id(), 403);

        $request->validate([
            'no'       => 'required|integer|min:1',
            'content'  => 'required|string',
            'language' => 'sometimes|string|max:50',
        ]);

        $chapter = Chapter::create([
            'volume_id' => $volume->id,
            'no'        => $request->no,
        ]);

        Content::create([
            'chapter_id'       => $chapter->id,
            'content'          => $request->content,
            'total_characters' => mb_strlen($request->content),
            'language'         => $request->language ?? 'id',
        ]);

        $volume->increment('chapters_total');

        return redirect()->route('addVolumesOrChapters', $volume->novel_id)->with('message', 'Chapter added!');
    }

    /** Update a volume */
    public function updateVolume(Request $request, $id)
    {
        $volume = Volume::with('novel')->findOrFail($id);
        abort_unless($volume->novel->author_id === Auth::id(), 403);

        $request->validate([
            'no' => 'required|integer|min:1',
        ]);

        $volume->update(['no' => $request->no]);

        return back()->with('message', 'Volume updated!');
    }

    /** Delete a volume */
    public function deleteVolume($id)
    {
        $volume = Volume::with('novel')->findOrFail($id);
        abort_unless($volume->novel->author_id === Auth::id(), 403);

        $novel = $volume->novel;
        $volume->delete();
        $novel->decrement('volumes_total');

        return back()->with('message', 'Volume deleted!');
    }

    /** Update a chapter */
    public function updateChapter(Request $request, $id)
    {
        $chapter = Chapter::with(['volume.novel', 'content'])->findOrFail($id);
        abort_unless($chapter->volume->novel->author_id === Auth::id(), 403);

        $request->validate([
            'no'       => 'required|integer|min:1',
            'content'  => 'required|string',
            'language' => 'sometimes|string|max:50',
        ]);

        $chapter->update(['no' => $request->no]);

        if ($chapter->content) {
            $chapter->content->update([
                'content'          => $request->content,
                'total_characters' => mb_strlen($request->content),
                'language'         => $request->language ?? 'id',
            ]);
        } else {
            Content::create([
                'chapter_id'       => $chapter->id,
                'content'          => $request->content,
                'total_characters' => mb_strlen($request->content),
                'language'         => $request->language ?? 'id',
            ]);
        }

        return back()->with('message', 'Chapter updated!');
    }

    /** Delete a chapter */
    public function deleteChapter($id)
    {
        $chapter = Chapter::with('volume.novel')->findOrFail($id);
        abort_unless($chapter->volume->novel->author_id === Auth::id(), 403);

        $volume = $chapter->volume;
        $chapter->delete();
        $volume->decrement('chapters_total');

        return back()->with('message', 'Chapter deleted!');
    }

    // ─────────────────────────────────────────────
    // ADMIN
    // ─────────────────────────────────────────────
    public function admin_dashboard()
    {
        $stats = [
            'users'    => User::count(),
            'novels'   => Novel::count(),
            'authors'  => Author::count(),
            'comments' => Comment::count(),
        ];

        $novels = Novel::with('author')->orderBy('created_at', 'desc')->get();
        $authors = Author::withCount('novels')->get();
        $users  = User::orderBy('name')->get(['id', 'name', 'email', 'role', 'created_at']);
        $carousels = \App\Models\Carousel::orderBy('id', 'desc')->get();

        return Inertia::render('admin/Dashboard', [
            'adminData' => $stats,
            'novels'    => $novels,
            'authors'   => $authors,
            'users'     => $users,
            'carousels' => $carousels,
        ]);
    }

    public function admin_store_carousel(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'link'  => 'nullable|string'
        ]);

        $path = $request->file('image')->store('carousels', 'public');
        \App\Models\Carousel::create([
            'image' => $path,
            'link'  => $request->link
        ]);

        return back()->with('message', 'Carousel added!');
    }

    public function admin_delete_carousel($id)
    {
        $carousel = \App\Models\Carousel::findOrFail($id);
        if ($carousel->image) Storage::disk('public')->delete($carousel->image);
        $carousel->delete();

        return back()->with('message', 'Carousel deleted!');
    }

    /** Admin: update any novel */
    public function admin_update_novel(Request $request, $id)
    {
        $novel = Novel::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status'      => 'sometimes|in:Ongoing,Completed,Hiatus',
            'cover'       => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            if ($novel->cover) Storage::disk('public')->delete($novel->cover);
            $validated['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $novel->update($validated);

        return redirect()->route('admin.dashboard')->with('message', 'Novel updated!');
    }

    /** Admin: delete any novel */
    public function admin_delete_novel($id)
    {
        $novel = Novel::findOrFail($id);
        if ($novel->cover) Storage::disk('public')->delete($novel->cover);
        $novel->delete();

        return redirect()->route('admin.dashboard')->with('message', 'Novel deleted!');
    }
}