<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class UseItemPacket extends InboundPacket{

	/** @var int */
	public $hand;

	public function pid(){
		return self::USE_ITEM_PACKET;
	}

	protected function decode(){
		$this->hand = $this->getVarInt();
	}
}