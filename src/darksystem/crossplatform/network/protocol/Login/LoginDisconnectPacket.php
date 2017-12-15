<?php

namespace darksystem\crossplatform\network\protocol\Login;

use darksystem\crossplatform\network\OutboundPacket;

class LoginDisconnectPacket extends OutboundPacket{

	/** @var string */
	public $reason;

	public function pid(){
		return self::LOGIN_DISCONNECT_PACKET;
	}

	protected function encode(){
		$this->putString($this->reason);
	}
}