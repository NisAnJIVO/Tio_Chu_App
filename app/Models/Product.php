<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'sale_price',
        'cost_price',
        'unit',
        'units_per_package',
        'stock_warehouse',
        'is_active',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'units_per_package' => 'integer',
        'stock_warehouse' => 'integer',
        'is_active' => 'boolean',
    ];

    public function barSales(): HasMany
    {
        return $this->hasMany(BarSale::class);
    }

    /**
     * Mapeo de Licores a su Mixer/Soda incluido en el Combo según el flujo de Tío Chu:
     * - Singanis => Ginger Ale 2.0L (id 23)
     * - Fernet y Rones => Coca Cola 2.0L (id 24)
     * - Whiskys => Agua Vital 2.0L (id 25)
     * - Gins => Agua Tónica 1.0L (id 26)
     * - Tequilas / Jagermeister => Solos (sin mixer)
     */
    public static function getMixerMapping(): array
    {
        return [
            // Singanis => 1 Ginger Ale 2.0L (id 23)
            1 => ['mixer_id' => 23, 'ratio' => 1],
            2 => ['mixer_id' => 23, 'ratio' => 1],
            3 => ['mixer_id' => 23, 'ratio' => 1],
            // Fernet y Rones => 1 Coca Cola 2.0L (id 24)
            4 => ['mixer_id' => 24, 'ratio' => 1],
            5 => ['mixer_id' => 24, 'ratio' => 1],
            6 => ['mixer_id' => 24, 'ratio' => 1],
            7 => ['mixer_id' => 24, 'ratio' => 1],
            8 => ['mixer_id' => 24, 'ratio' => 1],
            // Whiskys => 1 Agua Vital 2.0L (id 25)
            9 => ['mixer_id' => 25, 'ratio' => 1],
            10 => ['mixer_id' => 25, 'ratio' => 1],
            11 => ['mixer_id' => 25, 'ratio' => 1],
            12 => ['mixer_id' => 25, 'ratio' => 1],
            // Gins => 2 Aguas Tónicas 1.0L (id 26) - ¡2 botellas por combo!
            16 => ['mixer_id' => 26, 'ratio' => 2],
            17 => ['mixer_id' => 26, 'ratio' => 2],
            18 => ['mixer_id' => 26, 'ratio' => 2],
            19 => ['mixer_id' => 26, 'ratio' => 2],
            20 => ['mixer_id' => 26, 'ratio' => 2],
            21 => ['mixer_id' => 26, 'ratio' => 2],
            22 => ['mixer_id' => 26, 'ratio' => 2],
        ];
    }
}
