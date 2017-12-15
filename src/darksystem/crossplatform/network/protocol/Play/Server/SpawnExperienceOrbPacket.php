<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class SpawnExperienceOrbPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var float */
	public $x;
	/** @var float */
	public $y;
	/** @var float */
	public $z;
	/** @var int */
	public $count;

	public function pid(){
		return self::SPAWN_EXPERIENCE_ORB_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putDouble($this->x);
		$this->putDouble($this->y);
		$this->putDouble($this->z);
		$this->putShort($this->count);
	}
}
