<?php

declare(strict_types=1);

namespace FurkanYks\Olta\entity\projectile;

use FurkanYks\Olta\Main;
use FurkanYks\Olta\manager\Manager;
use FurkanYks\Olta\animation\FishHookPosition;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\item\ItemTypeIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\block\Water;
use pocketmine\world\sound\CauldronEmptyWaterSound;

class FishingHook extends Projectile
{

    protected $touchWaterSound = false;
    public CompoundTag $compTag;
    public $elapsedtime = 1;

    public static function getNetworkTypeId(): string
    {
        return EntityIds::FISHING_HOOK;
    }

    protected function getInitialDragMultiplier(): float
    {
        return 0;
    }

    protected function getInitialGravity(): float
    {
        return 0;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.25, 0.25);
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->compTag = $nbt;
        $this->setHasGravity(false);
        parent::initEntity($nbt);
        $this->compTag = $nbt;
    }

    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $shootingEntity, $nbt);
        if ($shootingEntity instanceof Player) {
            Manager::getInstance()->setFishing($shootingEntity, $this);
        } else {
            $this->flagForDespawn();
        }
    }


    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $player = $this->getOwningEntity();
        $speed = 0.1;

        $hxblocky = $this->getPosition()->getWorld()->getHighestBlockAt($this->getPosition()->getFloorX(), $this->getPosition()->getFloorZ());

        $x = $this->getPosition()->x - $this->getPosition()->x;
        $y = $hxblocky - $this->getPosition()->y + 1;
        $z = $this->getPosition()->z - $this->getPosition()->z;

        $distanceSquared = $x ** 2 + $z ** 2;

        if ($distanceSquared < 0.7) {
            $motionX = 0;
            $motionY = $y;
            $motionZ = 0;
        } else {
            $diff = abs($x) + abs($z);
            $motionX = $speed * 0.15 * ($x / $diff);
            $motionY = $speed * 0.15 * ($y / $diff);
            $motionZ = $speed * 0.15 * ($z / $diff);
        }
        $this->setMotion(new Vector3($motionX, $motionY, $motionZ));
        if ($player instanceof Player) {
            $playername = $player->getName();
            $this->unsetBubble($playername);
            if (isset(Main::$bubbletime[$playername])) {
                $this->broadcastFishHookPosition();
            }
            if (!isset(Main::$hooks[$playername])) {
                Main::$hooks[$playername] = time() + 1;
                if ($this->controlWater()) {
                    $zaman = $this->elapsedtime;
                    $player->sendPopup("§fElapsed time:§a {$zaman}§fs");
                    $this->elapsedtime++;
                }
                $this->controlDistance();
            }
            if (time() < Main::$hooks[$playername]) {
            } else {
                unset(Main::$hooks[$playername]);
            }
            if (!isset(Main::$fishingtime[$playername])) {
                $needwait = rand(4, Main::$max_waiting_time);
                Main::$fishingtime[$playername] = time() + $needwait;
                if ($this->controlWater()) {
                    if (isset(Main::$bubbletime[$playername])) {
                        $this->getWorld()->addSound($this->location, new CauldronEmptyWaterSound());
                    }
                }
            }
            if (time() < Main::$fishingtime[$playername]) {
            } else {
                unset(Main::$fishingtime[$playername]);
                $this->setBubbleTime($player);
            }
        }

        $despawn = false;

        if ($player instanceof Player) {

            if (
                $player->getInventory()->getItemInHand()->getTypeId() !== ItemTypeIds::FISHING_ROD ||
                !$player->isAlive() ||
                $player->isClosed() ||
                $player->getLocation()->getWorld()->getFolderName() !== $this->getLocation()->getWorld()->getFolderName()
            ) {
                $despawn = true;
            }
        } else {
            $despawn = true;
        }

        if ($despawn) {
            $this->flagForDespawn();
            $hasUpdate = true;
        }
        if ($this->controlWater()) {
            if (!$this->touchWaterSound) {
                $this->getWorld()->addSound($this->location, new CauldronEmptyWaterSound());
                $this->touchWaterSound = true;
            }
        }

        return $hasUpdate;
    }

    public function setBubbleTime(Player $player){
        $playername = $player->getName();
        if (!isset(Main::$bubbletime[$playername])) {
            Main::$bubbletime[$playername] = time() + 2;
        }
        if (time() < Main::$bubbletime[$playername]) {
        } else {
            unset(Main::$bubbletime[$playername]);
        }
    }

    public function flagForDespawn(): void
    {
        $owningEntity = $this->getOwningEntity();

        if ($owningEntity instanceof Player) {
            Manager::getInstance()->unsetFishing($owningEntity);
        if (isset(Main::$fishingtime[$owningEntity->getName()])) {
            unset(Main::$fishingtime[$owningEntity->getName()]);
        }
                Main::$x[$owningEntity->getName()] = $this->getPosition()->getFloorX();
                Main::$y[$owningEntity->getName()] = $this->getPosition()->getFloorY();
                Main::$z[$owningEntity->getName()] = $this->getPosition()->getFloorZ();

        }

        parent::flagForDespawn();
    }

    public function broadcastFishHookPosition()
    {
        $this->broadcastAnimation(new FishHookPosition($this));
    }

    public function controlWater()
    {
        $x = $this->getPosition()->getFloorX();
        $y = $this->getPosition()->getFloorY();
        $z = $this->getPosition()->getFloorZ();
        $world = $this->getWorld();
        $block = $world->getBlockAt($x, $y - 1, $z);
        if ($block instanceof Water) {
            return true;
        }
        return false;
    }

    public function controlDistance()
    {
        if ($this->getOwningEntity() instanceof Player) {
            if ($this->getOwningEntity()->getPosition()->distance($this->getPosition()) >= 32) {
                $this->flagForDespawn();
            }
        }
        return false;
    }
    public function unsetBubble(string $playername){
        if (isset(Main::$bubbletime[$playername])) {
            if (time() < Main::$bubbletime[$playername]) {
            } else {
                unset(Main::$bubbletime[$playername]);
            }
        }
    }
}