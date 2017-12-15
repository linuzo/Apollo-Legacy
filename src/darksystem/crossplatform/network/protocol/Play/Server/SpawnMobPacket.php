<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use darksystem\crossplatform\utils\Binary;

class SpawnMobPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var string */
	public $uuid;
	/** @var int */
	public $type;
	/** @var float */
	public $x;
	/** @var float */
	public $y;
	/** @var float */
	public $z;
	/** @var float */
	public $yaw;
	/** @var float */
	public $pitch;
	/** @var float */
	public $headPitch;
	/** @var float */
	public $velocityX;
	/** @var float */
	public $velocityY;
	/** @var float */
	public $velocityZ;
	/** @var array */
	public $metadata;

	public function pid(){
		return self::SPAWN_MOB_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->put($this->uuid);
		$this->putVarInt($this->type);
		$this->putDouble($this->x);
		$this->putDouble($this->y);
		$this->putDouble($this->z);
		$this->putAngle($this->yaw);
		$this->putAngle($this->pitch);
		$this->putAngle($this->headPitch);
		$this->putShort((int) round($this->velocityX * 8000));
		$this->putShort((int) round($this->velocityY * 8000));
		$this->putShort((int) round($this->velocityZ * 8000));
		$this->put(Binary::writeMetadata($this->metadata));
	}
}
