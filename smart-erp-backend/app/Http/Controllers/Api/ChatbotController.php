<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    /**
     * Handle chatbot conversation
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'context' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userMessage = strtolower($request->message);
            $response = $this->generateResponse($userMessage);

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $response['message'],
                    'type' => $response['type'],
                    'data' => $response['data'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate chatbot response based on user message
     */
    private function generateResponse($message)
    {
        // Product inquiry
        if (preg_match('/\b(produk|barang|stok|harga)\b/i', $message)) {
            return $this->handleProductInquiry($message);
        }

        // Store hours inquiry
        if (preg_match('/\b(jam buka|buka|tutup|operasional)\b/i', $message)) {
            return [
                'message' => 'Toko kami buka setiap hari Senin - Sabtu pukul 08:00 - 20:00 WIB, dan Minggu pukul 09:00 - 18:00 WIB. Ada yang bisa kami bantu?',
                'type' => 'info'
            ];
        }

        // Location inquiry
        if (preg_match('/\b(alamat|lokasi|dimana|tempat)\b/i', $message)) {
            return [
                'message' => 'Toko kami berlokasi di Jl. Raya Medan No. 123, Medan, Sumatera Utara. Anda dapat menggunakan GPS dengan koordinat yang tersedia di website kami. Apakah ada yang ingin ditanyakan lagi?',
                'type' => 'info'
            ];
        }

        // Order status
        if (preg_match('/\b(pesanan|order|status|transaksi)\b/i', $message)) {
            return [
                'message' => 'Untuk mengecek status pesanan, silakan berikan kode transaksi Anda atau hubungi customer service kami. Apakah Anda memiliki kode transaksi?',
                'type' => 'info'
            ];
        }

        // Contact inquiry
        if (preg_match('/\b(kontak|hubungi|telepon|whatsapp|email)\b/i', $message)) {
            return [
                'message' => "Anda dapat menghubungi kami melalui:\n📞 Telepon: +62 812-3456-7890\n📱 WhatsApp: +62 812-3456-7890\n✉️ Email: support@smarterp.com\nTim kami siap membantu Anda!",
                'type' => 'info'
            ];
        }

        // Greeting
        if (preg_match('/\b(halo|hai|hello|hi|selamat)\b/i', $message)) {
            return [
                'message' => 'Halo! Selamat datang di Smart ERP. Saya adalah asisten virtual yang siap membantu Anda. Ada yang bisa saya bantu hari ini?',
                'type' => 'greeting'
            ];
        }

        // Thank you
        if (preg_match('/\b(terima kasih|thanks|makasih)\b/i', $message)) {
            return [
                'message' => 'Sama-sama! Senang bisa membantu Anda. Jika ada pertanyaan lain, jangan ragu untuk bertanya ya! 😊',
                'type' => 'farewell'
            ];
        }

        // Default response
        return [
            'message' => "Maaf, saya belum mengerti pertanyaan Anda. Saya dapat membantu Anda dengan:\n• Informasi produk dan stok\n• Jam operasional toko\n• Lokasi toko\n• Status pesanan\n• Kontak customer service\n\nSilakan tanyakan hal-hal di atas!",
            'type' => 'help'
        ];
    }

    /**
     * Handle product-related inquiries
     */
    private function handleProductInquiry($message)
    {
        $products = Product::active()->get();
        
        foreach ($products as $product) {
            if (stripos($message, $product->name) !== false) {
                return [
                    'message' => "Produk {$product->name} tersedia dengan harga Rp " . number_format($product->price, 0, ',', '.') . ". Stok saat ini: {$product->stock} unit. Apakah Anda ingin memesan?",
                    'type' => 'product_info',
                    'data' => [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'category' => $product->category
                    ]
                ];
            }
        }

        // If no specific product mentioned, show top products
        $topProducts = Product::active()
            ->inRandomOrder()
            ->limit(3)
            ->get(['id', 'name', 'price', 'stock']);

        $productList = $topProducts->map(function($p) {
            return "• {$p->name} - Rp " . number_format($p->price, 0, ',', '.');
        })->implode("\n");

        return [
            'message' => "Berikut beberapa produk unggulan kami:\n{$productList}\n\nApakah Anda tertarik dengan salah satunya?",
            'type' => 'product_list',
            'data' => $topProducts
        ];
    }
}