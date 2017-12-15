<?php

namespace darksystem\crossplatform\entity;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\entity\Entity;
use pocketmine\tile\ItemFrame;
use pocketmine\utils\UUID;
use darksystem\crossplatform\network\protocol\Play\Server\DestroyEntitiesPacket;
use darksystem\crossplatform\network\protocol\Play\Server\SpawnObjectPacket;
use darksystem\crossplatform\network\protocol\Play\Server\EntityMetadataPacket;
use darksystem\crossplatform\utils\ConvertUtils;
use darksystem\crossplatform\DesktopPlayer;

class ItemFrameBlockEntity extends Position{
	
	/** @var array */
	protected static $itemFrames = [];
	/** @var array */
	protected static $itemFramesAt = [];
	/** @var array */
	protected static $itemFramesInChunk = [];

	/** @var array */
	private static $mapping = [
		0 => [ -90,  3],//EAST
		1 => [ +90,  1],//WEST
		2 => [   0,  0],//SOUTH
		3 => [-180,  2] //NORTH
	];

	/** @var int */
	private $eid;
	/** @var string */
	private $uuid;
	/** @var int */
	private $facing;
	/** @var int */
	private $yaw;

	/**
	 * @param Level $level
	 * @param int   $x
	 * @param int   $y
	 * @param int   $z
	 * @param int   $data
	 */
	private function __construct(Level $level, $x, $y, $z, $data){
		parent::__construct($x, $y, $z, $level);

		$prop = (new \ReflectionClass(Entity::class))->getProperty("entityCount");
		$prop->setAccessible(true);
		$this->eid = $prop->getValue();
		$prop->setValue($this->eid + 1);

		$this->uuid = UUID::fromRandom()->toBinary();
		$this->facing = $data;
		$this->yaw = self::$mapping[$data][0] ?? 0;
	}

	/**
	 * @return int
	 */
	public function getEntityId(){
		return $this->eid;
	}

	/**
	 * @return int
	 */
	public function getFacing(){
		return $this->facing;
	}

	/**
	 * @return bool
	 */
	public function hasItem(){
		$retval = false;
		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof ItemFrame){
			$retval = $tile->hasItem();
		}
		return $retval;
	}

	/**
	 * @param DesktopPlayer $player
	 */
	public function spawnTo(DesktopPlayer $player){
		$pk = new SpawnObjectPacket();
		$pk->eid = $this->eid;
		$pk->uuid = $this->uuid;
		$pk->type = SpawnObjectPacket::ITEM_FRAMES;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->yaw = $this->yaw;
		$pk->pitch = 0;
		$pk->data = self::$mapping[$this->facing][1];
		$pk->sendVelocity = true;
		$pk->velocityX = 0;
		$pk->velocityY = 0;
		$pk->velocityZ = 0;
		$player->putRawPacket($pk);

		$pk = new EntityMetadataPacket();
		$pk->eid = $this->eid;
		$pk->metadata = ["convert" => true];

		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof ItemFrame){
			$item = $tile->hasItem() ? $tile->getItem() : Item::get(Item::AIR, 0, 0);
			ConvertUtils::convertItemData(true, $item);
			$pk->metadata[6] = [5, $item];
			$pk->metadata[7] = [1, $tile->getItemRotation()];
		}

		$player->putRawPacket($pk);
	}

	/**
	 * @param DesktopPlayer $player
	 */
	public function despawnFrom(DesktopPlayer $player){
		$pk = new DestroyEntitiesPacket();
		$pk->ids []= $this->eid;
		$player->putRawPacket($pk);
	}

	public function despawnFromAll(){
		foreach($this->getLevel()->getChunkPlayers($this->x >> 4, $this->z >> 4) as $player){
			if($player instanceof DesktopPlayer){
				$this->despawnFrom($player);
			}
		}
		self::removeItemFrame($this);
	}

	/**
	 * @param Level $level
	 * @param int   $x
	 * @param int   $y
	 * @param int   $z
	 * @return bool
	 */
	public static function exists(Level $level, $x, $y, $z){
		return isset(self::$itemFramesAt[$level->getId()][Level::blockHash($x, $y, $z)]);
	}

	/**
	 * @param Level $level
	 * @param int   $x
	 * @param int   $y
	 * @param int   $z
	 * @param int   $data
	 * @param bool  $create
	 * @return ItemFrameBlockEntity|null
	 */
	public static function getItemFrame(Level $level, $x, $y, $z, $data = 0, $create = false){
		$entity = null;

		if(isset(self::$itemFramesAt[$level_id = $level->getId()][$index = Level::blockHash($x, $y, $z)])){
			$entity = self::$itemFramesAt[$level_id][$index];
		}elseif($create){
			$entity = new ItemFrameBlockEntity($level, $x, $y, $z, $data);
			self::$itemFrames[$level_id][$entity->eid] = $entity;
			self::$itemFramesAt[$level_id][$index] = $entity;

			if(!isset(self::$itemFramesInChunk[$level_id][$index = Level::chunkHash($x >> 4, $z >> 4)])){
				self::$itemFramesInChunk[$level_id][$index] = [];
			}
			self::$itemFramesInChunk[$level_id][$index] []= $entity;
		}

		return $entity;
	}

	/**
	 * @param Level $level
	 * @param int   $eid
	 * @return ItemFrameBlockEntity|null
	 */
	public static function getItemFrameById(Level $level, $eid){
		return self::$itemFrames[$level->getId()][$eid] ?? null;
	}

	/**
	 * @param Block $block
	 * @param bool  $create
	 * @return ItemFrameBlockEntity|null
	 */
	public static function getItemFrameByBlock(Block $block, $create=false){
		return self::getItemFrame($block->getLevel(), $block->x, $block->y, $block->z, $block->getDamage(), $create);
	}

	/**
	 * @param Level $level
	 * @param int   $x
	 * @param int   $z
	 */
	public static function getItemFramesInChunk(Level $level, $x, $z){
		return self::$itemFramesInChunk[$level->getId()][Level::chunkHash($x, $z)] ?? [];
	}

	/**
	 * @param ItemFrameBlockEntity $entity
	 */
	public static function removeItemFrame(ItemFrameBlockEntity $entity){
		unset(self::$itemFrames[$entity->level->getid()][$entity->eid]);
		unset(self::$itemFramesAt[$entity->level->getId()][Level::blockHash($entity->x, $entity->y, $entity->z)]);
		if(isset(self::$itemFramesInChunk[$level_id = $entity->getLevel()->getId()][$index = Level::chunkHash($entity->x >> 4, $entity->z >> 4)])){
			self::$itemFramesInChunk[$level_id][$index] = array_diff(self::$itemFramesInChunk[$level_id][$index], [$entity]);
		}
	}
}
