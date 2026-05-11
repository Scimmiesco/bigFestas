<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Item\ItemType;
use App\Enums\Item\ItemStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
