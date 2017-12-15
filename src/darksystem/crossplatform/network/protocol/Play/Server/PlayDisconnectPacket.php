<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class PlayDisconnectPacket extends OutboundPacket{

	/** @var string */
	public $reason;

	public function pid(){
		return self::PLAY_DISCONNECT_PACKET;
	}

	protected function encode(){
		$this->putString($this->reason);
	}
}