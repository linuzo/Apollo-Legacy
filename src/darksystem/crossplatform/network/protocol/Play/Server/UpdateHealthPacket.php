<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class UpdateHealthPacket extends OutboundPacket{

	/** @var float */
	public $health;
	/** @var int */
	public $food;
	/** @var float */
	public $saturation;

	public function pid(){
		return self::UPDATE_HEALTH_PACKET;
	}

	protected function encode(){
		$this->putFloat($this->health);
		$this->putVarInt($this->food);
		$this->putFloat($this->saturation);
	}
}