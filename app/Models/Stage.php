<?php

namespace App\Models;

use App\Http\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'day_id',
        'name',
        'slug',
        'cover',    
        'notes',    
        'description',  
        'curiosities',  
        'rating',   
        'address',  
        'lat',
        'lon', 
    ];

    public function day()
    {
       return $this->belongsTo(Day::class);
    }

    public function images()
    {
       return $this->hasMany(Image::class);
    }
}
