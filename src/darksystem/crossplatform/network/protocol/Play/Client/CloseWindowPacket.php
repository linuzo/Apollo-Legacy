<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class CloseWindowPacket extends InboundPacket{

	/** @var int */
	public $windowID;

	public function pid(){
		return self::CLOSE_WINDOW_PACKET;
	}

	protected function decode(){
		$this->windowID = $this->getByte();
	}
}