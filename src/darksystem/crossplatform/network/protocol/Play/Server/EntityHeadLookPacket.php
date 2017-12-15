<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityHeadLookPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $yaw;

	public function pid(){
		return self::ENTITY_HEAD_LOOK_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putAngle($this->yaw);
	}
}