<?php

namespace FurkanYks\Olta\manager;

use FurkanYks\Olta\event\EventListener;
use FurkanYks\Olta\Main;
use FurkanYks\Olta\entity\projectile\FishingHook;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class Manager {

    use SingletonTrait;

    private array $fishing = [];

    public function __construct()
    {
        self::setInstance($this);
        Main::getInstance()->getServer()->getPluginManager()->registerEvents(new EventListener(), Main::getInstance());

        EntityFactory::getInstance()->register(FishingHook::class, function(World $world, CompoundTag $nbt): FishingHook {
            return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        },                                     ['FishingHook']);
    }

    public function isFishing(Player $player): bool
    {
        return isset($this->fishing[$player->getPlayerInfo()->getUsername()]);
    }

    public function unsetFishing(Player $player): void
    {
        if($this->isFishing($player)) {
            unset($this->fishing[$player->getPlayerInfo()->getUsername()]);
        }
    }

    public function setFishing(Player $player, FishingHook $hook): void
    {
        if(!$this->isFishing($player)) {
            $this->fishing[$player->getPlayerInfo()->getUsername()] = $hook;
        }
    }

    public function getFishingHook(Player $player): ?FishingHook
    {
        return $this->fishing[$player->getPlayerInfo()->getUsername()];
    }
}