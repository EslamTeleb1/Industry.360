<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FooterItem extends Model
{
    protected $fillable = [
        'type', 'platform', 'label', 'url', 'image_path', 'order', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public static function socialLinks()
    {
        return static::where('type', 'social')
            ->where('active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($item) {
                return [
                    'platform' => $item->platform,
                    'url' => $item->url,
                    'label' => $item->label,
                    'active' => $item->active,
                    'order' => $item->order,
                    'id' => $item->id,
                ];
            })
            ->toArray();
    }

    public static function allItems()
    {
        return static::query()
            ->orderBy('type')
            ->orderBy('order')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'platform' => $item->platform,
                    'label' => $item->label,
                    'url' => $item->url,
                    'image_path' => $item->image_path,
                    'image_url' => $item->image_url,
                    'order' => $item->order,
                    'active' => $item->active,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            })
            ->toArray();
    }

    public static function imagesList()
    {
        return static::where('type', 'image')
            ->where('active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($item) {
                return $item->image_url;
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_path;

        if (! $path) {
            return null;
        }

        // Leave absolute URLs unchanged
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        $storageUrl = Storage::url($path);

        // If the storage driver already returned an absolute URL (e.g. S3), use it.
        if (preg_match('/^https?:\/\//i', $storageUrl)) {
            return $storageUrl;
        }

        // Make local storage URLs absolute using the app URL
        return asset($storageUrl);
    }
}
