<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Message extends Model
{
    // Define the fillable fields to allow mass assignment
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'group_id',
        'file_path',
        'sent_at',
        'read_at',
        'file_extension',
        'subtitle', // Subtitle is part of the fillable array
    ];

    // Cast sent_at and read_at to Carbon instances and manage timezone
    protected $dates = ['sent_at', 'read_at'];

    // Relationship with the sender (User model)
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relationship with the receiver (User model)
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Relationship with the group (Group model)
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // This will allow you to get all "read" events for the message
    public function reads()
    {
        return $this->hasMany(GroupMessageRead::class, 'message_id');
    }

    // Accessor to adjust the created_at attribute to a specific timezone (Asia/Karachi)
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Karachi');
    }

    // Accessor to adjust the sent_at attribute to a specific timezone (Asia/Karachi)
    public function getSentAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Karachi');
    }

    // You can create a custom accessor to handle subtitle in case you need to format it or manipulate it further
    public function getSubtitleAttribute($value)
    {
        return $value ? htmlspecialchars($value) : ''; // Optional: sanitize the subtitle if needed
    }

    // You can add any custom methods here for better handling if needed
}
