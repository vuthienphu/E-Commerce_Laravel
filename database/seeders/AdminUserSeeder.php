<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@gmail.com';

        // nếu đã tồn tại user với email này thì cập nhật role + password (hoặc bỏ qua)
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'name' => 'Administrator',
                'password' => Hash::make('admin@123'), // đổi mật khẩu nếu muốn
                'role' => 'admin', // nếu bạn có cột role
            ]);
        } else {
            User::create([
                'name' => 'Administrator',
                'email' => $email,
                'password' => Hash::make('admin@123'),
                'role' => 'admin', // nếu bảng users có cột role
            ]);
        }

        $this->command->info('Admin user created/updated: ' . $email);
    }
}
