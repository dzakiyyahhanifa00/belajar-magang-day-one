<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // GET ALL: Ambil semua data produk beserta relasi kategorinya
    public function index()
    {
        $products = Product::with('category')->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Products',
            'data'    => $products
        ], 200);
    }

    // CREATE: Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer'
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil ditambahkan',
            'data'    => $product
        ], 201);
    }

    // GET SINGLE: Detail 1 produk
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Product',
            'data'    => $product
        ], 200);
    }

    // UPDATE: Perbarui data produk
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer'
        ]);

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil diupdate',
            'data'    => $product
        ], 200);
    }

    // DELETE: Hapus produk
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil dihapus'
        ], 200);
    }
}