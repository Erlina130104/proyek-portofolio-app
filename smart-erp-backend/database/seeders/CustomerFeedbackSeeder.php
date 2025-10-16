<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerFeedback;

class CustomerFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $feedbacks = [
            [
                'customer_name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'phone' => '+6281234567890',
                'message' => 'Pelayanan sangat memuaskan! Produk berkualitas dan pengiriman cepat.',
                'rating' => 5,
                'sentiment' => 'positive',
                'status' => 'reviewed'
            ],
            [
                'customer_name' => 'Siti Nurhaliza',
                'email' => 'siti@gmail.com',
                'phone' => '+6281234567891',
                'message' => 'Produk bagus tapi pengiriman agak lambat.',
                'rating' => 3,
                'sentiment' => 'neutral',
                'status' => 'pending'
            ],
            [
                'customer_name' => 'Ahmad Dahlan',
                'email' => 'ahmad@gmail.com',
                'phone' => '+6281234567892',
                'message' => 'Sangat kecewa dengan kualitas produk. Tidak sesuai deskripsi.',
                'rating' => 1,
                'sentiment' => 'negative',
                'status' => 'pending'
            ],
            [
                'customer_name' => 'Dewi Lestari',
                'email' => 'dewi@gmail.com',
                'phone' => '+6281234567893',
                'message' => 'Luar biasa! Customer service sangat responsif dan membantu.',
                'rating' => 5,
                'sentiment' => 'positive',
                'status' => 'reviewed'
            ],
            [
                'customer_name' => 'Rizky Pratama',
                'email' => 'rizky@gmail.com',
                'phone' => '+6281234567894',
                'message' => 'Harga terlalu mahal untuk kualitas yang didapat.',
                'rating' => 2,
                'sentiment' => 'negative',
                'status' => 'pending'
            ],
        ];

        foreach ($feedbacks as $feedback) {
            CustomerFeedback::create($feedback);
        }
    }
}