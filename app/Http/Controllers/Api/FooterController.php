<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\FooterItem;
use App\Models\HomeSetting;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $homeSetting = HomeSetting::query()->first();

        return $this->successResponse([
            'items' => FooterItem::allItems(),
            // 'social_links' => FooterItem::socialLinks(),
            // 'images' => FooterItem::imagesList(),
            'home_description' => $homeSetting ? $homeSetting->getTranslation('description', app()->getLocale(), false) : null,
            'home_description_en' => $homeSetting ? $homeSetting->getTranslation('description', 'en', false) : null,
            'home_description_ar' => $homeSetting ? $homeSetting->getTranslation('description', 'ar', false) : null,
        ], 'Footer retrieved successfully');
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

        return $this->createdResponse([
            'item' => $item,
        ], 'Footer item created successfully');
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

        return $this->successResponse([
            'item' => $item,
        ], 'Footer item updated successfully');
    }

    public function destroy($id)
    {
        $item = FooterItem::findOrFail($id);
        $item->delete();

        return $this->successResponse(null, 'Footer item deleted successfully');
    }
}
