<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Market;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Market::create([
            "nama_toko" => "Sunda Food",
            "alamat" => "Kp.Cinusa  Girang",
            "no_tlp" => "087846775109",
            "bidang_usaha" => "Warung makanan cepat saji",
        ]);

        User::create([
            "nama" => "Ahmad Fauzan",
            "email" => "ahmad@gmail.com",
            "password" => "123",
            "role" => "admin",
            "code" => "928374",
            "id_toko" => 1
        ]);

        Category::create([
            "kategori" => "Makanan",
            "id_toko" => 1
        ]);

        Category::create([
            "kategori" => "Minuman",
            "id_toko" => 1
        ]);

        Category::create([
            "kategori" => "Cemilan",
            "id_toko" => 1
        ]);

        Product::create([
            "foto" => "burger.jpeg",
            "namaProduk" => "Burger",
            "id_kategori" => "1",
            "harga_beli" => 7000,
            "harga_jual" => 9000,
            "stok" => 10,
            "id_toko" => 1
        ]);

        Product::create([
            "foto" => "chicken.jpeg",
            "namaProduk" => "Fried Chicken",
            "id_kategori" => "1",
            "harga_beli" => 6000,
            "harga_jual" => 8000,
            "stok" => 10,
            "id_toko" => 1
        ]);

        Product::create([
            "foto" => "coca-cola.jpeg",
            "namaProduk" => "Coca Cola",
            "id_kategori" => "2",
            "harga_beli" => 4000,
            "harga_jual" => 6000,
            "stok" => 10,
            "id_toko" => 1
        ]);

        Product::create([
            "foto" => "french-fries.jpeg",
            "namaProduk" => "French Fries",
            "id_kategori" => "1",
            "harga_beli" => 7000,
            "harga_jual" => 8500,
            "stok" => 10,
            "id_toko" => 1
        ]);

        Product::create([
            "foto" => "sprite.jpg",
            "namaProduk" => "Sprite",
            "id_kategori" => "2",
            "harga_beli" => 4500,
            "harga_jual" => 6000,
            "stok" => 10,
            "id_toko" => 1
        ]);

        Product::create([
            "namaProduk" => "Pizza",
            "id_kategori" => "1",
            "harga_beli" => 20000,
            "harga_jual" => 30000,
            "stok" => 10,
            "id_toko" => 1
        ]);

        Product::create([
            "namaProduk" => "Le Mineral",
            "id_kategori" => "2",
            "harga_beli" => 3000,
            "harga_jual" => 5500,
            "stok" => 10,
            "id_toko" => 1
        ]);
    }
}
