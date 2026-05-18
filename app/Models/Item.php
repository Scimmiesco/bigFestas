<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Item\ItemType;
use App\Enums\Item\ItemStatus;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $fillable = ['nome', 'tipo', 'status', 'observacoes', 'team_id'];

    protected function casts(): array
    {
        return [
            'tipo' => ItemType::class,
            'status' => ItemStatus::class,
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function rentals(): BelongsToMany
        {
            return $this->belongsToMany(Rental::class, 'item_rental')
                    ->withTimestamps();
        }
}
