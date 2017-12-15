<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PlayerPositionPacket extends InboundPacket{

	/** @var float */
	public $x;
	/** @var float */
	public $y;
	/** @var float */
	public $z;
	/** @var bool */
	public $onGround;

	public function pid(){
		return self::PLAYER_POSITION_PACKET;
	}

	protected function decode(){
		$this->x = $this->getDouble();
		$this->y = $this->getDouble();
		$this->z = $this->getDouble();
		$this->onGround = $this->getBool();
	}
}
