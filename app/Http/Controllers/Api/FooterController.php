<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FooterItem;
use App\Models\HomeSetting;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    public function index()
    {
        $homeSetting = HomeSetting::query()->first();

        return response()->json([
            'items' => FooterItem::allItems(),
            'social_links' => FooterItem::socialLinks(),
            'images' => FooterItem::imagesList(),
            'home_description' => $homeSetting ? $homeSetting->getTranslation('description', app()->getLocale(), false) : null,
            'home_description_en' => $homeSetting ? $homeSetting->getTranslation('description', 'en', false) : null,
            'home_description_ar' => $homeSetting ? $homeSetting->getTranslation('description', 'ar', false) : null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:social,image',
            'platform' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:1000',
            'image_path' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:4096',
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        // If an image file was uploaded, store it and set image_path
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $stored = $file->store('footer', 'public');
            $data['image_path'] = $stored;
        }

        $item = FooterItem::create($data);

        return response()->json(['item' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $item = FooterItem::findOrFail($id);

        $data = $request->validate([
            'type' => 'sometimes|in:social,image',
            'platform' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:1000',
            'image_path' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:4096',
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        // If a new image file is uploaded, remove old stored file (if local) and store new one
        if ($request->hasFile('image')) {
            // delete old file if it looks like a stored path (not an absolute URL)
            if ($item->image_path && ! preg_match('/^https?:\/\//i', $item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }

            $file = $request->file('image');
            $stored = $file->store('footer', 'public');
            $data['image_path'] = $stored;
        }

        $item->update($data);

        return response()->json(['item' => $item]);
    }

    public function destroy($id)
    {
        $item = FooterItem::findOrFail($id);
        $item->delete();
        return response()->json(['deleted' => true]);
    }
}
