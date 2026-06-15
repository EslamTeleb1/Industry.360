<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FooterItem;
use App\Models\HomeSetting;

class FooterController extends Controller
{
    public function index()
    {
        $homeSetting = HomeSetting::query()->first();

        return response()->json([
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
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

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
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

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
