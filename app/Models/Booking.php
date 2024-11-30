<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'room_id', 'start_time', 'end_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function memo()
    {
        return $this->hasOne(MeetingMemo::class);
    }
}
