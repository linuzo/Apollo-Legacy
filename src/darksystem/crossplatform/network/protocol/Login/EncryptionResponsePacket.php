<?php

namespace darksystem\crossplatform\network\protocol\Login;

use darksystem\crossplatform\network\InboundPacket;

class EncryptionResponsePacket extends InboundPacket{

	/** @var string */
	public $sharedSecret;
	/** @var string */
	public $verifyToken;

	public function pid(){
		return self::ENCRYPTION_RESPONSE_PACKET;
	}

	protected function decode(){
		$this->sharedSecret = $this->get($this->getVarInt());
		$this->verifyToken = $this->get($this->getVarInt());
	}
}