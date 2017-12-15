<?php

namespace darksystem\crossplatform\network\protocol\Login;

use darksystem\crossplatform\network\InboundPacket;

class LoginStartPacket extends InboundPacket{

	/** @var string */
	public $name;

	public function pid(){
		return self::LOGIN_START_PACKET;
	}

	protected function decode(){
		$this->name = $this->getString();
	}
}