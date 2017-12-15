<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PlayerPositionAndLookPacket extends InboundPacket{

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
	public $onGround;

	public function pid(){
		return self::PLAYER_POSITION_AND_LOOK_PACKET;
	}

	protected function decode(){
		$this->x = $this->getDouble();
		$this->y = $this->getDouble();
		$this->z = $this->getDouble();
		$this->yaw = $this->getFloat();
		$this->pitch = $this->getFloat();
		$this->onGround = $this->getBool();
	}
}
