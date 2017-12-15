<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class CloseWindowPacket extends OutboundPacket{

	/** @var int */
	public $windowID;

	public function pid(){
		return self::CLOSE_WINDOW_PACKET;
	}

	protected function encode(){
		$this->putByte($this->windowID);
	}
}