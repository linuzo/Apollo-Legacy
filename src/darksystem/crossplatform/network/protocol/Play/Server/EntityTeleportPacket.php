<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityTeleportPacket extends OutboundPacket{
	
	/** @var int */
	public $eid;
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
	/** @var bool */
	public $onGround = true;

	public function pid(){
		return self::ENTITY_TELEPORT_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putDouble($this->x);
		$this->putDouble($this->y);
		$this->putDouble($this->z);
		$this->putAngle($this->yaw);
		$this->putAngle($this->pitch);
		$this->putBool($this->onGround);
	}
}
