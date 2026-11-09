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
        'initial_packages',
        'initial_units',
        'added_packages',
        'added_units',
        'night_packages',
        'night_units',
        'selected_special_mixer',
        'packages',
        'units',
        'total_initial',
        'saldo',
        'vendido',
        'unit_price',
        'subtotal',
        'stock_synced_vendido',
    ];

    protected $casts = [
        'initial_packages' => 'integer',
        'initial_units'    => 'integer',
        'added_packages'   => 'integer',
        'added_units'      => 'integer',
        'night_packages'   => 'integer',
        'night_units'      => 'integer',
        'packages'         => 'integer',
        'units'            => 'integer',
        'total_initial'    => 'integer',
        'saldo'            => 'integer',
        'vendido'          => 'integer',
        'unit_price'       => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'stock_synced_vendido' => 'integer',
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
        $unitsPerPkg = (int)($this->product?->units_per_package ?? 1);
        if ($unitsPerPkg < 1) $unitsPerPkg = 1;
        $this->total_initial = ((int)$this->packages * $unitsPerPkg) + (int)$this->units;
        $this->vendido = max(0, $this->total_initial - (int)$this->saldo);
        $this->subtotal = $this->vendido * (float)$this->unit_price;
    }

    /**
     * Formato Don Ludo para PAQUETES en planilla impresa:
     * - Saldo previo de ayer sin paréntesis: 1, 3, etc.
     * - Adición antes de abrir entre paréntesis: +(1), (3), etc.
     * - Adición en el transcurso de la noche separada: 1+(1) 1
     * - Si no hay nada: string vacío ""
     */
    public function getPrintPackageNotation(): string
    {
        $init  = (int)$this->initial_packages;
        $add   = (int)$this->added_packages;
        $night = (int)$this->night_packages;

        $parts = [];
        if ($init > 0 && $add > 0) {
            $parts[] = "{$init}+({$add})";
        } elseif ($init > 0) {
            $parts[] = "{$init}";
        } elseif ($add > 0) {
            $parts[] = "({$add})";
        }

        if ($night > 0) {
            $parts[] = "{$night}";
        }

        return implode(' ', $parts);
    }

    /**
     * Formato Don Ludo para UNIDADES en planilla impresa:
     * - Saldo previo de ayer sin paréntesis: 5, 2, 4, etc.
     * - Adición antes de abrir entre paréntesis: +(2), (12), (2), etc.
     * - Adición en el transcurso de la noche separada: 4+(2) 1
     * - Si no hay nada: string vacío ""
     */
    public function getPrintUnitNotation(): string
    {
        $init  = (int)$this->initial_units;
        $add   = (int)$this->added_units;
        $night = (int)$this->night_units;

        $parts = [];
        if ($init > 0 && $add > 0) {
            $parts[] = "{$init}+({$add})";
        } elseif ($init > 0) {
            $parts[] = "{$init}";
        } elseif ($add > 0) {
            $parts[] = "({$add})";
        }

        if ($night > 0) {
            $parts[] = "{$night}";
        }

        return implode(' ', $parts);
    }
}

