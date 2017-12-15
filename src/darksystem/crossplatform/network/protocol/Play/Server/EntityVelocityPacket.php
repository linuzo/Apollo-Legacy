<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityVelocityPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var float */
	public $velocityX;
	/** @var float */
	public $velocityY;
	/** @var float */
	public $velocityZ;

	public function pid(){
		return self::ENTITY_VELOCITY_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putShort((int) round($this->velocityX * 8000));
		$this->putShort((int) round($this->velocityY * 8000));
		$this->putShort((int) round($this->velocityZ * 8000));
	}
}
