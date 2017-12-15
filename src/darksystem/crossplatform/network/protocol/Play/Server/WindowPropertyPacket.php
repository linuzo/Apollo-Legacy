<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class WindowPropertyPacket extends OutboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $property;
	/** @var int */
	public $value;

	public function pid(){
		return self::WINDOW_PROPERTY_PACKET;
	}

	protected function encode(){
		$this->putByte($this->windowID);
		$this->putShort($this->property);
		$this->putShort($this->value);
	}
}