<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class KeepAlivePacket extends OutboundPacket{

	/** @var int */
	public $id;

	public function pid(){
		return self::KEEP_ALIVE_PACKET;
	}

	protected function encode(){
		$this->putLong($this->id);
	}
}