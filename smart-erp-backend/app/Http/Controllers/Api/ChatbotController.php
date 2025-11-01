<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    //  Wajib login (middleware Sanctum)
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    //  Fungsi utama chatbot
    public function chat(Request $request)
    {
        // Validasi input user
        $validator = Validator::make($request->all(), [
            'message' => 'required|string'
        ]);

        // Jika input salah → kirim error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil pesan user
        $userMessage = strtolower($request->message);

        // Proses respon otomatis
        $response = $this->generateResponse($userMessage);

        // Kirim hasil ke user
        return response()->json([
            'success' => true,
            'reply' => $response
        ]);
    }

    //  Logika chatbot
    private function generateResponse($message)
    {
        // Sambutan
        if (preg_match('/(halo|hai|hi|hello)/', $message)) {
            return "Halo! Ada yang bisa saya bantu?";
        }

        // Jam operasional
        if (preg_match('/jam|buka|tutup|operasional/', $message)) {
            return "Toko buka Senin–Sabtu 08:00–20:00, Minggu 09:00–18:00.";
        }

        // Lokasi toko
        if (preg_match('/lokasi|alamat|dimana/', $message)) {
            return "Kami berlokasi di Jl. Raya Medan No. 123, Medan.";
        }

        //  Cek produk dari database
        if (preg_match('/produk|barang|stok|harga/', $message)) {
            
            $product = Product::first(); // contoh aja, ambil 1 produk

            if ($product) {
                return "Contoh produk: {$product->name}, Harga: Rp ".number_format($product->price).", Stok: {$product->stock}.";
            }

            return "Saat ini belum ada produk di database.";
        }

        // Default jika chatbot belum ngerti
        return "Maaf, saya belum paham. Coba tanya tentang produk, harga, lokasi, atau jam buka.";
    }
}
