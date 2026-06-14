<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
                ];
            })
            ->toArray();
    }

    public static function imagesList()
    {
        return static::where('type', 'image')
            ->where('active', true)
            ->orderBy('order')
            ->pluck('image_path')
            ->toArray();
    }
}
