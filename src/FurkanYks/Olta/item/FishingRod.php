<?php

namespace FurkanYks\Olta\item;

use pocketmine\item\FishingRod as PMFishingRod;

class FishingRod extends PMFishingRod {

    public function getMaxDurability(): int
    {
        return 64;
    }
}