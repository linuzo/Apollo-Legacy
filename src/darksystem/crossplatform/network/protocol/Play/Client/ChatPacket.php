<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class ChatPacket extends InboundPacket{

	/** @var string */
	public $message;

	public function pid(){
		return self::CHAT_PACKET;
	}

	protected function decode(){
		$this->message = $this->getString();
	}
}