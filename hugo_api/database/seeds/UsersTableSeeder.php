<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::collection('users')->delete();

        DB::collection('users')::create(array(
            'first_name' => 'Miguel',
            'second_name' => 'Antonio',
            'lastname' => 'Mathus',
            'second_lastname' => 'Perez',
            'username' => 'miguel',
            'password' => Hash::make('hello'),
            'status' => 'ACTIVE',
            'role' => 'USER',
            'creation_date' => '21-05-2020 00:00:00',
            'last_update' => '21-05-2020 00:00:00',
         'seed' => true));
    }
}