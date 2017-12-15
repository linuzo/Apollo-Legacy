<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class ChatPacket extends OutboundPacket{

	/** @var string */
	public $message;
	/** @var int */
	public $position = 0;

	public function pid(){
		return self::CHAT_PACKET;
	}

	protected function encode(){
		$this->putString($this->message);
		$this->putByte($this->position);
	}
}