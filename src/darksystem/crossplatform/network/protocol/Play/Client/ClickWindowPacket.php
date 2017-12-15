<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;
use pocketmine\item\Item;

class ClickWindowPacket extends InboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $slot;
	/** @var int */
	public $button;
	/** @var int */
	public $actionNumber;
	/** @var int */
	public $mode;
	/** @var Item */
	public $clickedItem;

	public function pid(){
		return self::CLICK_WINDOW_PACKET;
	}

	protected function decode(){
		$this->windowID = $this->getByte();
		$this->slot = $this->getSignedShort();
		$this->button = $this->getSignedByte();
		$this->actionNumber = $this->getSignedShort();
		$this->mode = $this->getVarInt();
		$this->clickedItem = $this->getSlot();
	}
}
