<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityLookPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $yaw;
	/** @var int */
	public $pitch;
	/** @var bool */
	public $onGround;

	public function pid(){
		return self::ENTITY_LOOK_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putAngle($this->yaw);
		$this->putAngle($this->pitch);
		$this->putBool($this->onGround);
	}
}
