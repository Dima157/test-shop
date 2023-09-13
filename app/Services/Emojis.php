<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Psr\Http\Message\ServerRequestInterface;

class Emojis
{
    public function getEmojis(ServerRequestInterface $request) {
        $data = Validator::make(['productId' => $request->getQueryParams()['productId']],[
           'productId' => 'required|int'
        ]);
        $emojis = $this->emojiList();
        if ($data->fails()) {
            return $emojis;
        }

        $selectedEmoji = Auth::user()->emojis()->where('productId', $request->getQueryParams()['productId'])->first();
        if ($selectedEmoji) {
            return $emojis->map(function (\App\Models\Emojis $emoji) use ($selectedEmoji) {
                $emoji['isSelected'] = (int)$emoji->id == (int)$selectedEmoji->id;

                return $emoji;
            });
        }

        return $emojis;
    }

    public function emojiList() {
        $emojis = Cache::get('emojis');
        if ($emojis) {
            return $emojis;
        }

        $emojis = \App\Models\Emojis::select('emojis.id', 'emojis.unicode')
            ->addSelect(DB::raw('false as isSelected'))
            ->leftJoin('emojis_to_product', 'emojis_to_product.emojiId', '=', 'emojis.id')
            ->groupBy('emojis.id')
            ->orderByRaw('COUNT(emojis.id) DESC')
            ->get();
        Cache::set('emojis', $emojis);

        return $emojis;
    }
}
