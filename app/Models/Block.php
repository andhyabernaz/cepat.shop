<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $appends = ['image_url'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_show_title' => 'boolean'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? url('/upload/images/' . $this->image) : '';
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
