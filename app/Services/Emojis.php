<?php

namespace App\Services;

use App\Repositories\EmojiRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Emojis
{
    private EmojiRepository $emojiRepository;

    public function __construct()
    {
        $this->emojiRepository = new EmojiRepository();
    }

    public function productEmojis(int $productId): array
    {
        $emojis = $this->sortedListByPopular();
        if (!$productId) {
            return $emojis->toArray();
        }

        return $this->handleSelectedEmoji($emojis, $productId)->toArray();
    }

    public function emojiList(): Collection
    {
        return $this->emojiRepository->emojiList();
    }

    public function removeEmojisToProduct(int $productId): void
    {
        $this->emojiRepository->removeEmojisToProduct($productId);
    }

    public function getProductEmojisCount(int $productId): int {
        return $this->emojiRepository->getProductEmojisCount($productId)?->count ?? 0;
    }

    public function addEmojiToProduct(int $productId, $emojiId) {
        $selectedEmoji = $this->emojiRepository->userEmojiToProduct($productId);
        if($selectedEmoji) {
            throw new \LogicException('User already selected emoji');
        }
        $this->emojiRepository->addEmojiToProduct(Auth::id(), $productId, $emojiId);
    }

    private function handleSelectedEmoji(Collection $emojis, int $productId): Collection
    {
        $selectedEmoji = $this->emojiRepository->userEmojiToProduct($productId);
        if ($selectedEmoji) {
            return $emojis->map(function (\App\Models\Emojis $emoji) use ($selectedEmoji) {
                $emoji['isSelected'] = (int)$emoji->id == (int)$selectedEmoji->id;

                return $emoji;
            });
        }

        return $emojis;
    }

    private function sortedListByPopular(): Collection
    {
        //TODO Could be use cache with TTL
//        $emojis = Cache::get('emojis');
//        if ($emojis) {
//            return $emojis;
//        }
        $emojis = $this->emojiRepository->sortedListByPopular();
//        Cache::set('emojis', $emojis);

        return $emojis;
    }
}
