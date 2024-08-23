<?php

namespace App\Models;

use App\Http\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name', 
        'slug', 
        'cover',
        'description',
        'notes',
        'startDate',
        'endDate',
    ];

    public function days()
    {
        return $this->hasMany(Day::class);
    }
}
