<?php
namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $res = User::updateOrCreate(['email' => 'admin@gmail.com'], ['email' => 'admin@gmail.com', 'password' => Hash::make('12345')]);
        $res->assignRole('admin');
    }
}
