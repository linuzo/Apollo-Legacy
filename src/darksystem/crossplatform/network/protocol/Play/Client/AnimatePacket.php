<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class AnimatePacket extends InboundPacket{

	/** @var int  */
	public $hand;

	public function pid(){
		return self::ANIMATE_PACKET;
	}

	protected function decode(){
		$this->hand = $this->getVarInt();
	}
}