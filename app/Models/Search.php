<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $table = 'searchs';

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'keyword',
        'upper_price',
        'lower_price',
        'excluded_words',
        'status',
    ];

    public function excluded_words()
    {
        return $this->hasMany(SearchExculdedWord::class);
    }

    public function excluded_services()
    {
        return $this->hasMany(SearchExculdedService::class);
    }
    
}
