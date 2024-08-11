<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $equipment_details = Equipment_details::where('email', 'manirujjamanakash@gmail.com')->first();
        if (is_null($user)) {
            $user = new Equipment_details();
            $user->name = "Maniruzzaman Akash";
            $user->email = "manirujjamanakash@gmail.com";
            $user->password = Hash::make('12345678');
            $user->save();
        }
    }
}
