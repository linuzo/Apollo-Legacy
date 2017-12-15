<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;
use pocketmine\item\Item;

class CreativeInventoryActionPacket extends InboundPacket{

	/** @var int */
	public $slot;
	/** @var Item */
	public $item;

	public function pid(){
		return self::CREATIVE_INVENTORY_ACTION_PACKET;
	}

	protected function decode(){
		$this->slot = $this->getSignedShort();
		$this->item = $this->getSlot();
	}
}