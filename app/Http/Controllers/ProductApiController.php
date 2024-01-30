<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductApiController extends Controller
{
    // Fungsi untuk menampilkan daftar produk dengan paginasi
    public function index()
    {
        // Mengambil batas jumlah produk per halaman dari request atau menggunakan nilai default 10(intinya di limit)
        $limit = request()->input('limit', 10);

        // Mengambil data produk dengan menggunakan pagination ( ini dari bawaan laravelnya menggunakna eloquent kita tinggal gunakan fitur dari eloquent ini)
        $items = Product::paginate($limit);

        // Menyusun data dan meta informasi paginasi
        $data = $items->items();
        $meta = [
            'currentPage' => $items->currentPage(),
            'perPage' => $items->perPage(),
            'total' => $items->total(),
        ];

        // Menambahkan URL halaman berikutnya jika tersedia
        if ($items->hasMorePages()) {
            $meta['next_page_url'] = url($items->nextPageUrl());
        }

        // Menambahkan URL halaman sebelumnya jika halaman saat ini lebih dari 2
        if ($items->currentPage() > 2) {
            $meta['prev_page_url'] = url($items->previousPageUrl());
        }

        // Menambahkan URL halaman terakhir jika total halaman lebih dari 1
        if ($items->lastPage() > 1) {
            $meta['last_page_url'] = url($items->url($items->lastPage()));
        }

        // Mengembalikan respons JSON dengan data dan meta informasi paginasi(intinya meta ini untuk menghitung jumlah array, atau jumlah produk)
         return response()->json(['data' => $data, 'meta' => $meta], 200);
    }

    // Fungsi untuk menyimpan produk baru ke dalam database
    public function store(Request $request)
    {
        // Validasi data produk yang diterima dari request disitu tidak ada minimal karakter, cuma harus diisi dan berbentuk string atau numeric.
        $validatedData = $request->validate([
            'product_name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'description' => 'required|string',
        ]);

        // Membuat produk baru dan menyimpannya ke dalam database
        $item = Product::create($validatedData);

        // Mengembalikan respons JSON dengan data produk yang baru dibuat
        return response()->json(['data' => $item], 200);
    }

    // Fungsi untuk mengupdate data produk berdasarkan ID
    public function update(Request $request, $id)
    {
        // Validasi data produk yang akan diupdate
        $validatedData = $request->validate([
            'product_name' => 'string',
            'price' => 'numeric',
            'category' => 'string',
            'description' => 'string',
        ]);

        // Mencari produk berdasarkan ID, mengupdate data , dan mengembalikan respons JSON dengan data terupdate
        $item = Product::findOrFail($id);
        $item->update($validatedData);
        return response()->json(['data' => $item], 200);
    }

    // Fungsi untuk menampilkan detail produk atau bisa juga untuk dimunculkan kedalam form update cara kerja nya sih sama dengan get ke table
    public function show($id)
    {
        // Mencari produk berdasarkan ID dan mengembalikan respons JSON dengan data produk GetDataByID
        $item = Product::findOrFail($id);
        return response()->json(['data' => $item], 200);
    }

    // Fungsi untuk menghapus produk berdasarkan ID
    public function destroy($id)
    {
        // Mencari produk berdasarkan ID, menghapusnya, dan mengembalikan respons JSON dengan data produk yang dihapus
        $item = Product::findOrFail($id);
        $item->delete();
        return response()->json([
            'data' => $item
        ], 200);
    }
}
