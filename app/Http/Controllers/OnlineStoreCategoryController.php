<?php

namespace App\Http\Controllers;

use App\Models\OnlineStoreCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnlineStoreCategoryController extends Controller
{
    public function index()
    {
        $categories = OnlineStoreCategory::all();
        return view('online-store.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'featured' => 'nullable|string',
        ]);

        // Store the image in the public folder
        $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('category_images'), $imageName);

        // Create the category
        $category = new OnlineStoreCategory();
        $category->name = $request->name;
        $category->image = 'category_images/' . $imageName;
        $category->featured = $request->has('featured');
        $category->save();

        return redirect()->back()->with('success', 'Category created successfully');
    }


    public function edit($id)
    {
        $category = OnlineStoreCategory::findOrFail($id);
        return view('online-store.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'featured' => 'nullable|string',
        ]);

        $category = OnlineStoreCategory::findOrFail($id);
        $category->name = $request->name;

        // Update image if provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/category_images');
            $category->image = str_replace('public/', 'storage/', $imagePath);
        }

        // Update featured status
        $category->featured = $request->has('featured');

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = OnlineStoreCategory::findOrFail($id);

        // Delete the category image if it exists
        if ($category->image && Storage::exists(str_replace('storage/', 'public/', $category->image))) {
            Storage::delete(str_replace('storage/', 'public/', $category->image));
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }


}
