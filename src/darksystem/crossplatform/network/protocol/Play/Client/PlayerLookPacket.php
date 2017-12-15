<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PlayerLookPacket extends InboundPacket{

	/** @var float */
	public $yaw;
	/** @var float */
	public $pitch;
	/** @var bool */
	public $onGround;

	public function pid(){
		return self::PLAYER_LOOK_PACKET;
	}

	protected function decode(){
		$this->yaw = $this->getFloat();
		$this->pitch = $this->getFloat();
		$this->onGround = $this->getBool();
	}
}
