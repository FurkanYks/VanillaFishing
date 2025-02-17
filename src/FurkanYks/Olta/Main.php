<?php

namespace FurkanYks\Olta;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use FurkanYks\Olta\manager\Manager;

class Main extends PluginBase{

    use SingletonTrait;

    public static array $hooks = [];

    public static array $fishingtime = [];

    public static array $bubbletime = [];

    public static array $x = [];
    public static array $y = [];
    public static array $z = [];

    public static int $max_waiting_time = 60; # max fishing waiting time as seconds, example player can wait up to 60 seconds to catch a fish,

    public function onLoad(): void
    {
        self::setInstance($this);
        $this->getConfig();
    }

    public function onEnable(): void
    {
        (new Manager());
    }
}
