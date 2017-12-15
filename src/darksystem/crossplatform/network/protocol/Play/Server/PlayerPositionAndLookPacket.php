<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class PlayerPositionAndLookPacket extends OutboundPacket{

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
	/** @var int */
	public $flags = 0;
	/** @var int */
	public $teleportId = 0;

	public function pid(){
		return self::PLAYER_POSITION_AND_LOOK_PACKET;
	}

	protected function encode(){
		$this->putDouble($this->x);
		$this->putDouble($this->y);
		$this->putDouble($this->z);
		$this->putFloat($this->yaw);
		$this->putFloat($this->pitch);
		$this->putByte($this->flags);
		$this->putVarInt($this->teleportId);
	}
}
