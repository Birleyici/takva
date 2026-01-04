<?php

namespace App\Models;

use App\Models\Author;
use App\Models\Category;
use App\Models\Issue;
use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'issue_id',
        'author_id',
        'feature_image_id',
        'is_published',
        'view_count',
        'published_at',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'issue_id' => 'integer',
        'author_id' => 'integer',
        'feature_image_id' => 'integer',
        'is_published' => 'boolean',
        'view_count' => 'integer',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function featureImage()
    {
        return $this->belongsTo(MediaAsset::class, 'feature_image_id');
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
