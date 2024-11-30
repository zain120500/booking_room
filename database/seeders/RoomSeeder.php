<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run()
    {
        Room::create([
            'name' => 'Meeting Room 1',
            'floor' => 1,
            'capacity' => 10
        ]);
    }
}
