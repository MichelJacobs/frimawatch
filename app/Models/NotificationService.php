<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationService extends Model
{
    use HasFactory;

    protected $table = 'notification_services';

    protected $fillable = [
        'notification_id',
        'service',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
