<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLine extends Model
{
    use HasFactory;

    protected $table = 'register_url';

    protected $fillable = [
        'user_id',
        'itemName',
        'excluded_word',
        'itemImageUrl',
        'currentPrice',
        'url',
        'service',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
