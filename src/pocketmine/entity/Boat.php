<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\entity;

use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\math\Math;

class Boat extends Vehicle {

    const NETWORK_ID = 90;

    public $height = 0.7;
    public $width = 1.6;
    public $gravity = 0.5;
    public $drag = 0.1;

    /** @var boolean */
    public $canInteract = true;

    /**
     * Boat constructor.
     *
     * @param Level       $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt) {
        if (!isset($nbt->WoodID)) {
            $nbt->WoodID = new IntTag("WoodID", 0);
        }
        parent::__construct($level, $nbt);
        $this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $this->getWoodID());
    }

    public function initEntity() {
        parent::initEntity();
        $this->mountOffset = 1;
    }

    public function onInteract(Player $player, ItemItem $item): bool {
        if (!is_null($this->passenger)) {
            return false;
        }

        $this->mountEntity($player);
        return true;
    }

    public function getButtonText(): string {
        return "Board";
    }

    /**
     * @return int
     */
    public function getWoodID(): int {
        return (int) $this->namedtag["WoodID"];
    }

    /**
     * @param Player $player
     */
    public function spawnTo(Player $player) {
        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = Boat::NETWORK_ID;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->speedX = 0;
        $pk->speedY = 0;
        $pk->speedZ = 0;
        $pk->yaw = 0;
        $pk->pitch = 0;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }

    /**
     * @param $currentTick
     *
     * @return bool
     */
    public function onUpdate($currentTick) {
        if ($this->closed) {
            return false;
        }
        $tickDiff = $currentTick - $this->lastUpdate;
        if ($tickDiff <= 0 and ! $this->justCreated) {
            return true;
        }
        $this->lastUpdate = $currentTick;

        if ($this->isAlive()) {
            parent::onUpdate($currentTick);

            $this->timings->startTiming();
            $hasUpdate = $this->entityBaseTick($tickDiff);

            if ($this->level->getBlock(new Vector3($this->x, $this->y, $this->z))->getBoundingBox() !== null or $this->isInsideOfWater()) {
                $this->motionY = 0;
            } else {
                $this->motionY = -0.08;
            }
            $this->motionX *= 0.95;
            $this->motionZ *= 0.95;

            $this->move($this->motionX, $this->motionY, $this->motionZ);
            $this->updateMovement();

            if ($this->passenger == null) {
                if ($this->age > 1500) {
                    $this->close();
                    $hasUpdate = true;
                    $this->age = 0;
                }
                $this->age++;
            } else {
                $this->age = 0;
            }
            $this->timings->stopTiming();
        }

        return $hasUpdate or ! $this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
    }

    /**
     * @return array
     */
    public function getDrops() {
        return [
            ItemItem::get(ItemItem::BOAT, 0, 1)
        ];
    }

    /**
     * @return string
     */
    public function getSaveId() {
        $class = new \ReflectionClass(static::class);
        return $class->getShortName();
    }

}
