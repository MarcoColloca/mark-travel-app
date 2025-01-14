<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'stage_id',
        'url'
    ];
    
    public function day()
    {
        return $this->belongsTo(Day::class);
    }

}
