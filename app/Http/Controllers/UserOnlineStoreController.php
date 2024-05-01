<?php

namespace App\Http\Controllers;

use App\Models\OnlineStoreProduct;
use Illuminate\Http\Request;

class UserOnlineStoreController extends Controller
{
    // Controller method
    public function getProductDetails($productId) {
        $product = OnlineStoreProduct::with('onlineProductImages','product')->findOrFail($productId);
        return response()->json(['product' => $product]);
    }

}
