<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PlayerPacket extends InboundPacket{

	/** @var bool */
	public $onGround;

	public function pid(){
		return self::PLAYER_PACKET;
	}

	protected function decode(){
		$this->onGround = $this->getBool();
	}
}