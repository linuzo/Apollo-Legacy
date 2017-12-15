<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use pocketmine\item\Item;

class EntityEquipmentPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $slot;
	/** @var Item */
	public $item;

	public function pid(){
		return self::ENTITY_EQUIPMENT_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putVarInt($this->slot);
		$this->putSlot($this->item);
	}
}