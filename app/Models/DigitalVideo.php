<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalVideo extends Model
{
    use HasFactory;

     protected $fillable = [
        'video_title',
        'video_ratio',
        'video_duration',
        'video_description',
        'video_embed',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
