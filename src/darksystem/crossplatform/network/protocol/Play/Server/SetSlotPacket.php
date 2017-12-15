<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use pocketmine\item\Item;

class SetSlotPacket extends OutboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $slot;
	/** @var Item */
	public $item;

	public function pid(){
		return self::SET_SLOT_PACKET;
	}

	protected function encode(){
		$this->putByte($this->windowID);
		$this->putShort($this->slot);
		$this->putSlot($this->item);
	}
}
