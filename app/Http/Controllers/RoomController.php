<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    // Menampilkan daftar ruangan
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    // Menampilkan detail ruangan berdasarkan ID
    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        return response()->json($room);
    }

    // Membuat ruangan baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'floor' => 'required|integer',
            'capacity' => 'required|integer|min:1',
        ]);

        $room = Room::create($request->all());

        return response()->json($room, 201);
    }

    // Memperbarui data ruangan
    public function update(Request $request, $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'floor' => 'sometimes|required|integer',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        $room->update($request->all());

        return response()->json($room);
    }

    // Menghapus ruangan
    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $room->delete();

        return response()->json(['message' => 'Room deleted successfully']);
    }
}
