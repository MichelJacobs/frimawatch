<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchService extends Model
{
    use HasFactory;
    protected $table = 'search_services';

    protected $fillable = [
        'search_id',
        'service',
    ];

    public function search()
    {
        return $this->belongsTo(Search::class);
    }
}
