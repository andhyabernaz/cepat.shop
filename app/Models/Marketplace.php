<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'icon_path',
        'url',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public $appends = ['icon', 'can_display'];

    public function getIconAttribute()
    {
        if (!$this->icon_path && $this->is_default) {
            return url('/mp_icons/' . $this->provider . '.svg');
        }
        return url($this->icon_path);
    }

    public function getCanDisplayAttribute()
    {
        return $this->url && $this->is_active ? true : false;
    }

    public static function createDefault()
    {
        $items = [
            'Shopee',
            'Tokopedia',
            'TikTok',
            'Lazada',
            'Bukalapak'
        ];

        foreach ($items as $item) {
            static::updateOrCreate([
                'provider' => $item
            ], [
                'provider' => $item,
                'is_default' => 1
            ]);
        }
    }
}
