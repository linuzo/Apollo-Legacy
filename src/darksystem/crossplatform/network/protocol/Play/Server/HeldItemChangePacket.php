<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class HeldItemChangePacket extends OutboundPacket{

	/** @var int */
	public $selectedSlot;

	public function pid(){
		return self::HELD_ITEM_CHANGE_PACKET;
	}

	protected function encode(){
		$this->putByte($this->selectedSlot);
	}
}