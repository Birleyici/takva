<?php

namespace App\Models;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'year',
        'month',
        'description',
        'cover_image_id',
        'pdf_path',
        'pdf_original_name',
        'pdf_size',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'pdf_size' => 'integer',
        'cover_image_id' => 'integer',
    ];

    protected $appends = [
        'pdf_url',
        'month_name',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }

        return Storage::disk('public')->url($this->pdf_path);
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Ocak',
            2 => 'Şubat',
            3 => 'Mart',
            4 => 'Nisan',
            5 => 'Mayıs',
            6 => 'Haziran',
            7 => 'Temmuz',
            8 => 'Ağustos',
            9 => 'Eylül',
            10 => 'Ekim',
            11 => 'Kasım',
            12 => 'Aralık',
        ];

        return $months[$this->month] ?? (string) $this->month;
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_image_id');
    }
}
