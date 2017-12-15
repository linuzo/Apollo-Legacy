<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class KeepAlivePacket extends InboundPacket{

	/** @var int */
	public $id;

	public function pid(){
		return self::KEEP_ALIVE_PACKET;
	}

	protected function decode(){
		$this->id = $this->getLong();
	}
}