<?php

namespace darksystem\crossplatform\network\protocol\Login;

use darksystem\crossplatform\network\OutboundPacket;

class LoginSuccessPacket extends OutboundPacket{

	/** @var string */
	public $uuid;
	/** @var string */
	public $name;

	public function pid(){
		return self::LOGIN_SUCCESS_PACKET;
	}

	protected function encode(){
		$this->putString($this->uuid);
		$this->putString($this->name);
	}
}