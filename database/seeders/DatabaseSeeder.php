<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $this->call([
//
//        ]);
//
//        $faker = Faker::create('pt_BR');
//
//
//        User::query()->updateOrCreate([
//            'email' => 'admin@admincentral.com',
//        ], [
//            'name' => $faker->name,
//            'email' => 'admin@admincentral.com',
//            'google_id' => config('app.google_id'),
//            'email_verified_at' => now(),
//            'password' => Hash::make('123'),
//            'cellphone' => $faker->phoneNumber,
//        ]);
    }
}
