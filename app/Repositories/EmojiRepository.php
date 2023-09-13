<?php

namespace App\Repositories;

use App\Models\Emojis;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmojiRepository
{
    public function userEmojiToProduct(int $productId): Emojis|null {
        return Auth::user()->emojis()
            ->where('productId', $productId)
            ->first();
    }

    public function sortedListByPopular(): Collection {
        return \App\Models\Emojis::select('emojis.id', 'emojis.unicode')
            ->addSelect(DB::raw('false as isSelected'))
            ->leftJoin('emojis_to_product', 'emojis_to_product.emojiId', '=', 'emojis.id')
            ->groupBy('emojis.id')
            ->orderByRaw('COUNT(emojis.id) DESC')
            ->get();
    }

    public function emojiList(): Collection {
        return Emojis::all();
    }

    public function removeEmojisToProduct(int $productId): void {
        DB::table('emojis_to_product')
            ->where('productId', $productId)
            ->delete();
    }

    public function getProductEmojisCount(int $productId) {
        return DB::table('emojis_to_product')
            ->select(DB::raw('COUNT(*) as count'))
            ->where('productId', $productId)
            ->groupBy('productId')
            ->get()
            ->first();
    }

    public function addEmojiToProduct(int $userId, int $productId, int $emojiId) {
        DB::table('emojis_to_product')->insert([
            'userId' => $userId,
            'productId' => $productId,
            'emojiId' => $emojiId
        ]);
    }
}
