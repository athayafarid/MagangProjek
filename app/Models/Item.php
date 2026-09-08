<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'barcode',
        'name',
        'category_id',
        'description',
        'unit_id',
        'stock',
        'minimum_stock',
        'location_id',
        'supplier_id',
        'image',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Habis';
        }
        if ($this->stock < $this->minimum_stock) {
            return 'Stok Rendah';
        }
        if ($this->stock == $this->minimum_stock) {
            return 'Perhatian';
        }
        return 'Aman';
    }

    public function getStockBadgeClassAttribute(): string
    {
        return match ($this->stock_status) {
            'Aman' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'Perhatian' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Stok Rendah' => 'bg-rose-100 text-rose-800 border-rose-300',
            'Habis' => 'bg-slate-800 text-white border-slate-900',
            default => 'bg-slate-100 text-slate-800',
        };
    }
}
