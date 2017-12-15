<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use pocketmine\item\Item;

class WindowItemsPacket extends OutboundPacket{

	/** @var int */
	public $windowID;
	/** @var Item[] */
	public $items = [];

	public function pid(){
		return self::WINDOW_ITEMS_PACKET;
	}

	protected function encode(){
		$this->putByte($this->windowID);
		$this->putShort(count($this->items));
		foreach($this->items as $item){
			$this->putSlot($item);
		}
	}
}