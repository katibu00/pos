<?php

namespace App\Http\Controllers;

use App\Models\OnlineProductImage;
use App\Models\OnlineStoreCategory;
use App\Models\OnlineStoreProduct;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnlineStoreProductController extends Controller
{
    public function copy($id)
    {
        $stock = Stock::findOrFail($id);
        $categories = OnlineStoreCategory::all();
        return view('online-store.copy', compact('stock','categories'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'original_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description_content' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discounted_price' => 'nullable|numeric|min:0',
            'featured' => 'nullable|string|in:on',
            'apply_discount' => 'nullable|string|in:on',

        ]);

        // Create the online store product
        $product = OnlineStoreProduct::create([
            'stock_id' => $request->stock_id,
            'original_price' => $request->original_price,
            'selling_price' => $request->selling_price,
            'discount_price' => $request->discounted_price,
            'description' => $request->description_content,
            'category_id' => $request->category,
            'discount_applied' => $request->has('apply_discount') ? true : false,
            'featured' => $request->has('featured') ? true : false,
        ]);

        // Upload and attach images
        if ($request->hasFile('images')) {
            $featuredImageUploaded = false;
            foreach ($request->file('images') as $image) {
                $imageName = $image->getClientOriginalName();
                $image->move(public_path('online_product_images'), $imageName); // Move the image to the public directory

                // Get the image URL
                $imageUrl = 'online_product_images/' . $imageName;

                // Determine if this is the first image uploaded and set it as featured
                if (!$featuredImageUploaded) {
                    $featured = true;
                    $featuredImageUploaded = true;
                } else {
                    $featured = false;
                }

                OnlineProductImage::create([
                    'online_product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'featured' => $featured,
                ]);
            }
        }

        return response()->json(['message' => 'Product copied to online store successfully'], 200);
    }

    public function index()
    {
        $products = OnlineStoreProduct::all();
        return view('online-store.index', compact('products'));
    }

    public function edit(OnlineStoreProduct $product)
    {
        $categories = OnlineStoreCategory::all();
        return view('online-store.edit', compact('product','categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'original_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description_content' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discounted_price' => 'nullable|numeric|min:0',
            'featured' => 'nullable|string|in:on',
            'apply_discount' => 'nullable|string|in:on',

        ]);

        // Find the product
        $product = OnlineStoreProduct::findOrFail($id);

        // Update the product fields
        $product->original_price = $request->original_price;
        $product->selling_price = $request->selling_price;
        $product->description = $request->description_content;
        $product->category_id = $request->category_id;
        $product->discount_price = $request->discounted_price;
        $product->discount_applied = $request->has('apply_discount');
        $product->featured = $request->has('featured');

        // Upload and attach images if provided
        if ($request->hasFile('images')) {
            $featuredImageUploaded = false;
            foreach ($request->file('images') as $image) {
                $imageName = $image->getClientOriginalName();
                $image->move(public_path('online_product_images'), $imageName); // Move the image to the public directory

                // Get the image URL
                $imageUrl = 'online_product_images/' . $imageName;

                // Determine if this is the first image uploaded and set it as featured
                if (!$featuredImageUploaded) {
                    $featured = true;
                    $featuredImageUploaded = true;
                } else {
                    $featured = false;
                }

                OnlineProductImage::create([
                    'online_product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'featured' => $featured,
                ]);
            }
        }

        // Save the updated product
        $product->save();

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function deleteImage(Request $request, $id)
    {
        try {
            // Find the image by its ID
            $image = OnlineProductImage::findOrFail($id);

            // Delete the image from the database
            $image->delete();

            // Optionally, you can also delete the physical file from your server if needed
            // Make sure to use the appropriate storage path based on your setup

            // Return a success response
            return response()->json(['message' => 'Image deleted successfully'], 200);
        } catch (\Exception $e) {
            // Handle any errors and return an error response
            return response()->json(['message' => 'Failed to delete image'], 500);
        }
    }

    public function destroy($id)
    {
        // Find the product
        $product = OnlineStoreProduct::findOrFail($id);

        // Delete associated images from storage and database
        foreach ($product->onlineProductImages as $image) {
            // Delete image file from storage
            Storage::delete($image->image_url);

            // Delete image record from database
            $image->delete();
        }

        // Delete the product itself
        $product->delete();

        return redirect()->route('online-store.products')->with('success', 'Product deleted successfully.');
    }

}
