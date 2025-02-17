<?php

namespace FurkanYks\Olta\translator;

class Translator
{
    private array $translations = [
        "Raw Salmon" => "Raw Salmon",
        "Bone" => "Bone",
        "Enchanted Book" => "Enchanted Book",
        "Pufferfish" => "Pufferfish",
        "Raw Fish" => "Raw Fish",
        "Ink Sac" => "Ink Sac",
        "Bow" => "Bow",
        "Fishing Rod" => "Fishing Rod",
        "Leather Boots" => "Leather Boots",
        "Stick" => "Stick",
        "Tripwire Hook" => "Tripwire Hook",
        "Potion" => "Potion",
        "Rotten Flesh" => "Rotten Flesh",
        "Bowl" => "Bowl",
        "Nautilus Shell" => "Nautilus Shell",
        "Bamboo" => "Bamboo",
        "Lily Pad" => "Lily Pad",
        "Leather" => "Leather",
        "String" => "String",
        "Clownfish" => "Clownfish",
    ];

    public function translate(string $name): string
    {
        return $this->translations[$name] ?? "Raw Salmon";
    }
}