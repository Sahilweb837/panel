<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'department_id',
        'meeting_date',
        'start_time',
        'end_time',
        'meeting_mode',
        'meeting_link',
        'location',
        'created_by',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    public function minutes()
    {
        return $this->hasMany(MeetingMinute::class);
    }

    public function messages()
    {
        return $this->hasMany(MeetingMessage::class);
    }

    public function files()
    {
        return $this->hasMany(MeetingFile::class);
    }
}
