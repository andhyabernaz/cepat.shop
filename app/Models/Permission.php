<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'ability',
        'desc',
        'slug',
        'module',
    ];

    const MODULES = [
        [
            'name' => 'Product',
            'desc' => 'Kelola Akses Produk',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Order',
            'desc' => 'Kelola Akses Pesanan',
            'abilities' => [
                'View',
                'Create',
                'Update',
                'Delete',
                'Accept Payment',
                'Follow Up',
                'Manage',
                'Finish',
                'Cancel'
            ]
        ],
        [
            'name' => 'Promo',
            'desc' => 'Kelola Akses Promo',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Voucher',
            'desc' => 'Kelola Akses Voucher',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Payment Account',
            'desc' => 'Kelola Akses Metode Pembayaran',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Category',
            'desc' => 'Kelola Akses Kategori',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Mutasi Saldo',
            'desc' => 'List Mutasi Saldo',
            'abilities' => ['View', 'Manage']
        ],
        [
            'name' => 'Withdrawal',
            'desc' => 'List Withdrawal',
            'abilities' => ['View', 'Manage']
        ],
        [
            'name' => 'Affiliate',
            'desc' => 'Kelola Akses Affiliate',
            'abilities' => ['View', 'Manage']
        ],
        [
            'name' => 'Content',
            'desc' => 'Kelola Akses Konten Artikel, Slider dan Block',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Config',
            'desc' => 'Kelola Akses Pengaturan',
            'abilities' => [
                'View',
                'Basic',
                'Order',
                'Shipping',
                'SMTP',
                'Whatsapp Gateway',
                'Payment Gateway',
                'Meta',
                'Affiliate',
                'Saldo',
                'Marketplace',
                'System',
            ]
        ],
        [
            'name' => 'Notification',
            'desc' => 'Kelola Akses Notifikasi',
            'abilities' => ['View', 'Create', 'Update', 'Delete', 'Send']
        ],
        [
            'name' => 'Store',
            'desc' => 'Kelola Akses Toko',
            'abilities' => ['View','Update']
        ],
        [
            'name' => 'User',
            'desc' => 'Kelola Akses User',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Role',
            'desc' => 'Kelola Akses Role',
            'abilities' => ['View', 'Create', 'Update', 'Delete']
        ],
        [
            'name' => 'Permission',
            'desc' => 'Kelola Role Pemission',
            'abilities' => ['View', 'Manage']
        ],
        [
            'name' => 'Review',
            'desc' => 'Kelola Ulasan',
            'abilities' => ['View', 'Manage','Delete']
        ],
        [
            'name' => 'Media',
            'desc' => 'Kelola Akses File Media',
            'abilities' => ['View','Upload', 'Delete']
        ],
    ];

    public static function generateDefaultPermissions()
    {
          foreach (self::MODULES as $module) {
            foreach ($module['abilities'] as $c) {
                $ability = trim($c) . " " . trim($module['name']);
                if(static::where('ability', $ability)->count() == 0) {
                    static::create([
                        'ability' => $ability,
                        'desc' => $module['desc'],
                        'slug' => Str::slug($ability),
                        'module' => $module['name']
                    ]);
                }
            }
        }
    }
}
