<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Emojis extends Model
{
    use HasFactory;
    protected $table='emojis';
    public $isSelected = false;

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'emojis_to_product', 'emojiId');
    }
}
