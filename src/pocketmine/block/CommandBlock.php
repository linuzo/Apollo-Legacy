<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\tile\CommandBlock as TileCB;

class CommandBlock extends Solid{
	
	protected $id = self::COMMAND_BLOCK;

	/**
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(){
		return true;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Command Block";
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return -1;
	}

	public function isBreakable(Item $item){
        return false;
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        if(!($player instanceof Player) && !$player->isOp() && !$player->isCreative()){
            return false;
        }
        $pitch = $player->pitch;
        if(abs($pitch) >= 60){
            if($pitch < 0){
                $f = 4;
            }else{
                $f = 5;
            }
        }else{
            $f = ($player->getDirection() - 1) & 0x03;
        }
        $faces = [
            0 => 4,
            1 => 2,
            2 => 5,
            3 => 3,
            4 => 0,
            5 => 1
        ];
        $this->meta = $faces[$f];
        $this->level->setBlock($this, $this);
        $nbt = new CompoundTag("", [
            new StringTag("id", Tile::COMMAND_BLOCK),
            new IntTag("x", $this->x),
            new IntTag("y", $this->y),
            new IntTag("z", $this->z),
            new IntTag("blockType", $this->getBlockType())
        ]);
        Tile::createTile(Tile::COMMAND_BLOCK, $this->level, $nbt);
        return true;
    }

    public function onActivate(Item $item, Player $player = null){
        if(!($player instanceof Player) || !$player->isOp() or !$player->isCreative()){
            return false;
        }
        $tile = $this->getTile();
        if(!$tile instanceof TileCB){
            $nbt = new CompoundTag("", [
                new StringTag("id", Tile::COMMAND_BLOCK),
                new IntTag("x", $this->x),
                new IntTag("y", $this->y),
                new IntTag("z", $this->z),
                new IntTag("blockType", $this->getBlockType())
            ]);
            $tile = Tile::createTile(Tile::COMMAND_BLOCK, $this->level, $nbt);
        }
        $tile->spawnTo($player);
        $tile->show($player);
        return true;
    }

    public function setPowered($powered){
        if(($tile = $this->getTile()) != null){
            $tile->setPowered($powered);
        }
    }

    public function getBlockType(){
        return TileCB::NORMAL;
    }
    
    public function getTile(){
        return $this->level->getTile($this);
    }

    public function getResistance(){
        return 18000000;
    }

    public function onUpdate($type){
        if($type == Level::BLOCK_UPDATE_NORMAL || $type == Level::BLOCK_UPDATE_REDSTONE){
            $this->setPowered($this->level->isBlockPowered($this));
        }
    }
}
