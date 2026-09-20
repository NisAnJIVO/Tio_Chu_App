<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'night_session_id',
        'product_id',
        'bar_name',
        'packages',
        'units',
        'total_initial',
        'saldo',
        'vendido',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'packages' => 'integer',
        'units' => 'integer',
        'total_initial' => 'integer',
        'saldo' => 'integer',
        'vendido' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function nightSession(): BelongsTo
    {
        return $this->belongsTo(NightSession::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Recalcula totales de inventario y subtotal monetario
     */
    public function calculateTotals(): void
    {
        // Total inicial en botellas = paquetes + unidades
        $this->total_initial = (int)$this->packages + (int)$this->units;
        $this->vendido = max(0, $this->total_initial - (int)$this->saldo);
        $this->subtotal = $this->vendido * (float)$this->unit_price;
    }
}
