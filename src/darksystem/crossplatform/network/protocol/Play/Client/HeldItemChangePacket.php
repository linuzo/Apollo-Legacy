<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class HeldItemChangePacket extends InboundPacket{

	/** @var int */
	public $selectedSlot;

	public function pid(){
		return self::HELD_ITEM_CHANGE_PACKET;
	}

	protected function decode(){
		$this->selectedSlot = $this->getSignedShort();
	}
}