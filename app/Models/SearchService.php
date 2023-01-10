<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchExculdedService extends Model
{
    use HasFactory;
    protected $table = 'search_services';

    public function search()
    {
        return $this->belongsTo(Search::class);
    }
}
