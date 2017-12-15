<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class OpenWindowPacket extends OutboundPacket{

	/** @var int */
	public $windowID;
	/** @var string */
	public $inventoryType;
	/** @var string */
	public $windowTitle;
	/** @var int */
	public $slots;
	/** @var int */
	public $entityId = -1;

	public function pid(){
		return self::OPEN_WINDOW_PACKET;
	}

	protected function encode(){
		$this->putByte($this->windowID);
		$this->putString($this->inventoryType);
		$this->putString($this->windowTitle);
		$this->putByte($this->slots);
		if($this->entityId !== -1){
			$this->putInt($this->entityId);
		}
	}
}
