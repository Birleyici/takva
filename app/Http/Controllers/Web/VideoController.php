<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\VideoCategory;
use App\Models\Video;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        $selectedCategorySlug = request()->query('category');
        $selectedCategory = null;

        $videosQuery = Video::query()
            ->with('category')
            ->where('is_active', true)
            ->orderByDesc('created_at');

        if ($selectedCategorySlug) {
            $selectedCategory = VideoCategory::query()
                ->where('slug', $selectedCategorySlug)
                ->where('is_active', true)
                ->first();

            if ($selectedCategory) {
                $videosQuery->where('video_category_id', $selectedCategory->id);
            }
        }

        $videos = $videosQuery->paginate(9)->withQueryString();

        $videoCategories = VideoCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('videolar.index', [
            'videos' => $videos,
            'videoCategories' => $videoCategories,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(Video $video): View
    {
        abort_unless($video->is_active, 404);
        $video->loadMissing('category');

        return view('videolar.show', [
            'video' => $video,
        ]);
    }
}
