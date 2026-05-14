<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Admin UMKM Tempe',
                'email'    => 'admin@umkm-tempe.com',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );
        $this->command->info('✅ Admin → username: admin | password: admin123');
    }
}
