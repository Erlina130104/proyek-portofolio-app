<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Attendance;
use App\Models\SupportTicket;
use App\Models\CustomerFeedback;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n🌱 Seeding database...\n\n";

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@smarterp.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now()
        ]);

        echo "✓ Admin user created\n";

        // Create Products
        $products = [
            [
                'name' => 'Laptop Gaming ROG',
                'description' => 'Laptop gaming dengan spesifikasi tinggi',
                'sku' => 'LPT-001',
                'price' => 15000000,
                'stock' => 25,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Mouse Wireless Logitech',
                'description' => 'Mouse wireless dengan baterai tahan lama',
                'sku' => 'MSE-001',
                'price' => 350000,
                'stock' => 50,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Keyboard Mechanical RGB',
                'description' => 'Keyboard mechanical dengan lampu RGB',
                'sku' => 'KBD-001',
                'price' => 850000,
                'stock' => 35,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Monitor LED 24 inch',
                'description' => 'Monitor LED Full HD 24 inch',
                'sku' => 'MON-001',
                'price' => 2500000,
                'stock' => 20,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Headset Gaming',
                'description' => 'Headset gaming dengan microphone',
                'sku' => 'HDS-001',
                'price' => 450000,
                'stock' => 8,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Tas Ransel Anti Air',
                'description' => 'Tas ransel untuk laptop dengan material anti air',
                'sku' => 'BAG-001',
                'price' => 275000,
                'stock' => 45,
                'category' => 'Fashion',
                'status' => 'active'
            ],
            [
                'name' => 'Powerbank 20000mAh',
                'description' => 'Powerbank kapasitas besar dengan fast charging',
                'sku' => 'PWR-001',
                'price' => 185000,
                'stock' => 60,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Kabel USB Type-C',
                'description' => 'Kabel USB Type-C 1 meter',
                'sku' => 'CBL-001',
                'price' => 45000,
                'stock' => 100,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Webcam HD 1080p',
                'description' => 'Webcam untuk meeting online',
                'sku' => 'WBC-001',
                'price' => 550000,
                'stock' => 15,
                'category' => 'Elektronik',
                'status' => 'active'
            ],
            [
                'name' => 'Speaker Bluetooth',
                'description' => 'Speaker portable dengan bass kuat',
                'sku' => 'SPK-001',
                'price' => 320000,
                'stock' => 30,
                'category' => 'Elektronik',
                'status' => 'active'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        echo "✓ " . count($products) . " Products created\n";

        // Create Customers
        $customers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567890',
                'address' => 'Jl. Gatot Subroto No. 45, Medan',
                'type' => 'regular',
                'loyalty_points' => 0
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'phone' => '081234567891',
                'address' => 'Jl. Ahmad Yani No. 12, Medan',
                'type' => 'member',
                'loyalty_points' => 150
            ],
            [
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad@example.com',
                'phone' => '081234567892',
                'address' => 'Jl. Sudirman No. 78, Medan',
                'type' => 'vip',
                'loyalty_points' => 500
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi@example.com',
                'phone' => '081234567893',
                'address' => 'Jl. Imam Bonjol No. 23, Medan',
                'type' => 'regular',
                'loyalty_points' => 50
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewi@example.com',
                'phone' => '081234567894',
                'address' => 'Jl. Diponegoro No. 56, Medan',
                'type' => 'member',
                'loyalty_points' => 200
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        echo "✓ " . count($customers) . " Customers created\n";

        // Create Employees
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john@smarterp.com',
                'phone' => '081234567801',
                'position' => 'Manager',
                'department' => 'Operations',
                'salary' => 8000000,
                'join_date' => '2023-01-15',
                'status' => 'active'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@smarterp.com',
                'phone' => '081234567802',
                'position' => 'Cashier',
                'department' => 'Sales',
                'salary' => 4500000,
                'join_date' => '2023-03-20',
                'status' => 'active'
            ],
            [
                'name' => 'Robert Wilson',
                'email' => 'robert@smarterp.com',
                'phone' => '081234567803',
                'position' => 'IT Support',
                'department' => 'IT',
                'salary' => 6000000,
                'join_date' => '2023-02-10',
                'status' => 'active'
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria@smarterp.com',
                'phone' => '081234567804',
                'position' => 'Sales Staff',
                'department' => 'Sales',
                'salary' => 4000000,
                'join_date' => '2023-06-01',
                'status' => 'active'
            ],
            [
                'name' => 'David Lee',
                'email' => 'david@smarterp.com',
                'phone' => '081234567805',
                'position' => 'Warehouse Staff',
                'department' => 'Warehouse',
                'salary' => 3500000,
                'join_date' => '2023-07-15',
                'status' => 'active'
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        echo "✓ " . count($employees) . " Employees created\n";

        // Create Transactions
        $productsList = Product::all();
        $customersList = Customer::all();
        
        echo "⏳ Creating transactions...\n";
        
        for ($i = 0; $i < 50; $i++) {
            $customer = $customersList->random();
            $transactionDate = Carbon::now()->subDays(rand(0, 90));
            
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'total_amount' => 0,
                'discount' => rand(0, 100000),
                'final_amount' => 0,
                'payment_method' => ['cash', 'transfer', 'e-wallet'][rand(0, 2)],
                'status' => 'completed',
                'transaction_date' => $transactionDate
            ]);

            // Add 1-4 items to transaction
            $itemCount = rand(1, 4);
            $totalAmount = 0;
            
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $productsList->random();
                $quantity = rand(1, 3);
                $subtotal = $product->price * $quantity;
                $totalAmount += $subtotal;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ]);
            }

            $finalAmount = $totalAmount - $transaction->discount;
            $transaction->update([
                'total_amount' => $totalAmount,
                'final_amount' => $finalAmount
            ]);
        }

        echo "✓ 50 Transactions created\n";

        // Create Attendance records for last 30 days
        $employeesList = Employee::all();
        
        echo "⏳ Creating attendance records...\n";
        
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            
            foreach ($employeesList as $employee) {
                // 90% chance of present/late, 10% chance of absent/leave
                $rand = rand(1, 100);
                
                if ($rand <= 80) {
                    $status = 'present';
                    $checkIn = $date->copy()->setTime(8, rand(0, 30), 0);
                    $checkOut = $date->copy()->setTime(17, rand(0, 30), 0);
                } elseif ($rand <= 90) {
                    $status = 'late';
                    $checkIn = $date->copy()->setTime(9, rand(0, 30), 0);
                    $checkOut = $date->copy()->setTime(17, rand(0, 30), 0);
                } else {
                    $status = ['absent', 'leave', 'sick'][rand(0, 2)];
                    $checkIn = null;
                    $checkOut = null;
                }

                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status
                ]);
            }
        }

        echo "✓ Attendance records created\n";

        // Create Support Tickets
        $tickets = [
            [
                'user_id' => $admin->id,
                'title' => 'Printer tidak berfungsi',
                'description' => 'Printer di ruang kasir tidak dapat mencetak struk. Sudah dicek kabel dan masih tidak bisa.',
                'priority' => 'high',
                'status' => 'in_progress',
                'category' => 'Hardware'
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Sistem kasir lambat',
                'description' => 'Aplikasi kasir mengalami lag saat jam sibuk. Loading sangat lama.',
                'priority' => 'medium',
                'status' => 'open',
                'category' => 'Software'
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Request akses admin',
                'description' => 'Mohon dibuatkan akses admin untuk staff baru bernama Ahmad.',
                'priority' => 'low',
                'status' => 'open',
                'category' => 'Access'
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Komputer mati mendadak',
                'description' => 'Komputer di gudang sering mati mendadak. Perlu dicek PSU atau motherboard.',
                'priority' => 'urgent',
                'status' => 'open',
                'category' => 'Hardware'
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Jaringan internet lambat',
                'description' => 'Koneksi internet sangat lambat sejak kemarin. Mohon dicek router.',
                'priority' => 'high',
                'status' => 'resolved',
                'category' => 'Network'
            ],
        ];

        foreach ($tickets as $ticket) {
            SupportTicket::create($ticket);
        }

        echo "✓ " . count($tickets) . " Support tickets created\n";

        // Create Customer Feedbacks
        $feedbacks = [
            [
                'customer_id' => $customersList[0]->id,
                'product_id' => $productsList[0]->id,
                'rating' => 5,
                'comment' => 'Produk sangat bagus! Pengiriman cepat dan barang sesuai deskripsi. Highly recommended!',
                'ai_sentiment' => 'positive',
                'status' => 'reviewed'
            ],
            [
                'customer_id' => $customersList[1]->id,
                'product_id' => $productsList[1]->id,
                'rating' => 4,
                'comment' => 'Barang oke, tapi harga agak mahal dibanding toko lain.',
                'ai_sentiment' => 'neutral',
                'status' => 'reviewed'
            ],
            [
                'customer_id' => $customersList[2]->id,
                'product_id' => $productsList[2]->id,
                'rating' => 5,
                'comment' => 'Recommended! Pelayanan memuaskan dan kualitas produk mantap.',
                'ai_sentiment' => 'positive',
                'status' => 'pending'
            ],
            [
                'customer_id' => $customersList[3]->id,
                'product_id' => $productsList[3]->id,
                'rating' => 3,
                'comment' => 'Produk standar aja, tidak terlalu istimewa.',
                'ai_sentiment' => 'neutral',
                'status' => 'reviewed'
            ],
            [
                'customer_id' => $customersList[4]->id,
                'product_id' => $productsList[4]->id,
                'rating' => 2,
                'comment' => 'Kecewa, produk tidak sesuai ekspektasi. Kurang puas.',
                'ai_sentiment' => 'negative',
                'status' => 'pending'
            ],
            [
                'customer_id' => $customersList[0]->id,
                'product_id' => $productsList[5]->id,
                'rating' => 5,
                'comment' => 'Tas nya bagus banget! Kuat dan banyak kantongnya.',
                'ai_sentiment' => 'positive',
                'status' => 'reviewed'
            ],
        ];

        foreach ($feedbacks as $feedback) {
            CustomerFeedback::create($feedback);
        }

        echo "✓ " . count($feedbacks) . " Customer feedbacks created\n";
        
        echo "\n========================================\n";
        echo "✅ Database seeding completed successfully!\n";
        echo "========================================\n";
        echo "📋 Summary:\n";
        echo "   • Users: 1\n";
        echo "   • Products: " . count($products) . "\n";
        echo "   • Customers: " . count($customers) . "\n";
        echo "   • Employees: " . count($employees) . "\n";
        echo "   • Transactions: 50\n";
        echo "   • Support Tickets: " . count($tickets) . "\n";
        echo "   • Feedbacks: " . count($feedbacks) . "\n";
        echo "========================================\n";
        echo "🔐 Login credentials:\n";
        echo "   Email: admin@smarterp.com\n";
        echo "   Password: password123\n";
        echo "========================================\n\n";
    }
}