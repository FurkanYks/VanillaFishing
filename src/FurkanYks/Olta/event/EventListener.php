<?php

namespace FurkanYks\Olta\event;

use FurkanYks\Olta\entity\projectile\FishingHook;
use FurkanYks\Olta\item\Items;
use FurkanYks\Olta\Main;
use FurkanYks\Olta\manager\Manager;
use FurkanYks\Olta\translator\Translator;
use pocketmine\block\Air;
use pocketmine\block\Water;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Location;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Durable;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\sound\ThrowSound;

class EventListener implements Listener {

    public function onPlayerItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($item->getTypeId() !== ItemTypeIds::FISHING_ROD || !($item instanceof Durable)) {
            return;
        }

        if ($player->hasItemCooldown($item)) {
            $event->cancel();
            return;
        }

        $player->resetItemCooldown($item, 8);
        $manager = Manager::getInstance();

        if (!$manager->isFishing($player)) {
            $location = $player->getLocation();
            $world = $player->getWorld();
            $targetPos = $this->getTargetPosition($player, rand(6, 10));

            $block = $world->getBlockAt($targetPos->getFloorX(), $targetPos->getFloorY(), $targetPos->getFloorZ());

            if ($block instanceof Water || $block instanceof Air) {
                $hook = new FishingHook(Location::fromObject($targetPos, $world, $location->yaw, $location->pitch), $player);
                $hook->spawnToAll();
                $world->addSound($location, new ThrowSound());
                $item->applyDamage(1);
                $player->getInventory()->setItemInHand($item);
            }
        } else {
            $hook = $manager->getFishingHook($player);

            if (!$hook->isFlaggedForDespawn()) {
                $hook->flagForDespawn();
                $this->handleFishingDrop($player);
            }
        }

        $player->broadcastAnimation(new ArmSwingAnimation($player));
    }

    private function getTargetPosition(Player $player, float $distance = 6.0): Vector3 {
        return $player->getEyePos()->addVector($player->getDirectionVector()->multiply($distance));
    }

    private function handleFishingDrop(Player $player): void {
        $playerName = $player->getName();

        if (!isset(Main::$bubbletime[$playerName])) {
            return;
        }

        if (isset(Main::$x[$playerName], Main::$y[$playerName], Main::$z[$playerName])) {
            $pos = new Position(Main::$x[$playerName], Main::$y[$playerName], Main::$z[$playerName], $player->getWorld());

            $item_drop = (new Items)->getFishingDropItem();
            $item_drop_entity = clone $item_drop;
            $item_drop_entity->setLore(["fishing_animation_item"]);
            $item_drop_entity->setCount(1);

            $itemEntity = new ItemEntity(Location::fromObject($pos->asVector3()->add(0,2,0), $player->getWorld(), lcg_value() * 360, 0), $item_drop_entity);
            $itemEntity->setPickupDelay(300);
            $itemEntity->setDespawnDelay(60);
            $itemEntity->setNameTag((new Translator())->translate($item_drop->getName()));
            $itemEntity->setNameTagVisible();
            $itemEntity->setNameTagAlwaysVisible();
            $itemEntity->setHasGravity(false);
            $itemEntity->spawnToAll();

            if ($player->getInventory()->canAddItem($item_drop)) {
                $player->getInventory()->addItem($item_drop);
            } else {
                $pos->getWorld()->dropItem($pos->asVector3()->add(0, 2, 0), $item_drop);
            }
        }

        unset(Main::$bubbletime[$playerName]);
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $manager = Manager::getInstance();

        if ($manager->isFishing($player)) {
            $manager->unsetFishing($player);
        }
    }
}
