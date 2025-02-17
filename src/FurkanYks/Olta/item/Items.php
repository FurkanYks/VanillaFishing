<?php

namespace FurkanYks\Olta\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Items {

    private array $drops;

    public function __construct() {
        $this->drops = [
            1 => [VanillaItems::ENCHANTED_BOOK(), VanillaEnchantments::UNBREAKING()],
            2 => [VanillaItems::ENCHANTED_BOOK(), VanillaEnchantments::EFFICIENCY()],
            3 => [VanillaItems::ENCHANTED_BOOK(), VanillaEnchantments::PROTECTION()],
            4 => [VanillaItems::ENCHANTED_BOOK(), VanillaEnchantments::SHARPNESS()],
            5 => [VanillaItems::BOW(), VanillaEnchantments::POWER()],
            7 => VanillaItems::NAUTILUS_SHELL(), 8 => VanillaItems::NAUTILUS_SHELL(), 9 => VanillaItems::NAUTILUS_SHELL(), 10 => VanillaItems::NAUTILUS_SHELL(),
            11 => VanillaItems::BAMBOO(), 12 => VanillaItems::BAMBOO(), 13 => VanillaItems::BAMBOO(), 14 => VanillaItems::BAMBOO(), 15 => VanillaItems::BAMBOO(),
            16 => VanillaBlocks::LILY_PAD()->asItem(), 17 => VanillaBlocks::LILY_PAD()->asItem(), 18 => VanillaBlocks::LILY_PAD()->asItem(), 19 => VanillaBlocks::LILY_PAD()->asItem(), 20 => VanillaBlocks::LILY_PAD()->asItem(),
            21 => VanillaItems::BOWL(), 22 => VanillaItems::BOWL(), 23 => VanillaItems::BOWL(), 24 => VanillaItems::BOWL(), 25 => VanillaItems::BOWL(),
            26 => VanillaItems::LEATHER(), 27 => VanillaItems::LEATHER(), 28 => VanillaItems::LEATHER(), 29 => VanillaItems::LEATHER(), 30 => VanillaItems::LEATHER(),
            36 => VanillaItems::ROTTEN_FLESH(), 37 => VanillaItems::ROTTEN_FLESH(), 38 => VanillaItems::ROTTEN_FLESH(), 39 => VanillaItems::ROTTEN_FLESH(), 40 => VanillaItems::ROTTEN_FLESH(),
            41 => VanillaItems::POTION(), 42 => VanillaItems::POTION(), 43 => VanillaItems::POTION(), 44 => VanillaItems::POTION(), 45 => VanillaItems::POTION(),
            46 => VanillaItems::BONE(), 47 => VanillaItems::BONE(), 48 => VanillaItems::BONE(), 49 => VanillaItems::BONE(), 50 => VanillaItems::BONE(),
            51 => VanillaBlocks::TRIPWIRE_HOOK()->asItem(), 52 => VanillaBlocks::TRIPWIRE_HOOK()->asItem(), 53 => VanillaBlocks::TRIPWIRE_HOOK()->asItem(), 54 => VanillaBlocks::TRIPWIRE_HOOK()->asItem(), 55 => VanillaBlocks::TRIPWIRE_HOOK()->asItem(),
            56 => VanillaItems::STRING(), 57 => VanillaItems::STRING(), 58 => VanillaItems::STRING(), 59 => VanillaItems::STRING(), 60 => VanillaItems::STRING(),
            61 => VanillaItems::INK_SAC(), 62 => VanillaItems::INK_SAC(), 63 => VanillaItems::INK_SAC(), 64 => VanillaItems::INK_SAC(), 65 => VanillaItems::INK_SAC(),
            71 => VanillaItems::STICK(), 72 => VanillaItems::STICK(), 73 => VanillaItems::STICK(), 74 => VanillaItems::STICK(), 75 => VanillaItems::STICK()
        ];
    }

    public function getFishingDropItem(): Item {
        $chance = rand(1, 450);

        if (isset($this->drops[$chance])) {
            $drop = $this->drops[$chance];
            if (is_array($drop)) {
                $item = clone $drop[0];
                $item->addEnchantment(new EnchantmentInstance($drop[1], rand(1, 3)));
                return $item;
            }
            return clone $drop;
        }

        return match (true) {
            $chance >= 76 && $chance <= 200 => VanillaItems::RAW_SALMON(),
            $chance >= 202 && $chance <= 250 => VanillaItems::PUFFERFISH(),
            $chance >= 252 && $chance <= 300 => VanillaItems::CLOWNFISH(),
            default => VanillaItems::RAW_FISH()
        };
    }
}
