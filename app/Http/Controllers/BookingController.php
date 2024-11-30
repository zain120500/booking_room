<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Participant;
use App\Models\MeetingMemo;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    // Menampilkan daftar booking
    public function index()
    {
        $userId = auth()->id(); // ID user yang sedang login

        // Ambil booking yang memiliki peserta sesuai dengan user
        $bookings = Booking::with([
            'room',
            'participants',
            'memo',
        ])
        ->whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId); // Pastikan booking memiliki participant yang sesuai dengan user
        })
        ->get();
    
        return response()->json($bookings);
    }
    // Membuat booking baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user(); // User yang sedang login
        $room = Room::findOrFail($request->room_id);

        // Memeriksa jika ruangan sudah dibooking pada periode yang sama
        $existingBooking = Booking::where('room_id', $room->id)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->first();

        if ($existingBooking) {
            return response()->json(['error' => 'Room already booked at this time'], 400);
        }

        // Membuat booking baru
        $booking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        Participant::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
        ]);
        // Tambahkan peserta lain jika ada
        if ($request->has('participants')) {
            foreach ($request->participants as $participantId) {
                Participant::create([
                    'booking_id' => $booking->id,
                    'user_id' => $participantId,
                ]);
            }
        }

        return response()->json($booking, 201);
    }

    // Menambahkan memo hasil meeting
    public function addMemo(Request $request, $bookingId)
    {
        $request->validate([
            'memo' => 'required|string',
        ]);

        $memo = MeetingMemo::create([
            'booking_id' => $bookingId,
            'memo' => $request->memo
        ]);

        return response()->json($memo, 201);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $roomId = $booking->room_id;

        // Periksa jika salah satu waktu (start_time atau end_time) diberikan
        if ($request->filled('start_time') || $request->filled('end_time')) {
            // Tetapkan waktu baru atau gunakan waktu yang ada jika tidak diubah
            $startTime = $request->start_time ?? $booking->start_time;
            $endTime = $request->end_time ?? $booking->end_time;

            // Periksa konflik dengan booking lain
            $existingBooking = Booking::where('room_id', $roomId)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->where('id', '!=', $booking->id) // Abaikan booking ini
                ->first();

            if ($existingBooking) {
                return response()->json(['error' => 'Room already booked at this time'], 400);
            }
        }

        // Perbarui data booking
        $booking->update([
            'start_time' => $request->start_time ?? $booking->start_time,
            'end_time' => $request->end_time ?? $booking->end_time,
        ]);

    
        if ($request->has('participants')) {
            $booking->participants()->delete(); // Hapus peserta lama
            foreach ($request->participants as $participantId) {
                Participant::create([
                    'booking_id' => $booking->id,
                    'user_id' => $participantId,
                ]);
            }
        }

        return response()->json($booking);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->participants()->delete();

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }
}
