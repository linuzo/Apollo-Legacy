<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use darksystem\crossplatform\utils\Binary;

class SpawnPlayerPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var string */
	public $uuid;
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
	/** @var array */
	public $metadata;

	public function pid(){
		return self::SPAWN_PLAYER_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->put($this->uuid);
		$this->putDouble($this->x);
		$this->putDouble($this->y);
		$this->putDouble($this->z);
		$this->putAngle($this->yaw);
		$this->putAngle($this->pitch);
		$this->put(Binary::writeMetadata($this->metadata));
	}
}
