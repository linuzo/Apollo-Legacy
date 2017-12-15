<?php

namespace darksystem\crossplatform\network\protocol\Login;

use darksystem\crossplatform\network\OutboundPacket;

class EncryptionRequestPacket extends OutboundPacket{

	/** @var string */
	public $serverID;
	/** @var string */
	public $publicKey;
	/** @var string */
	public $verifyToken;

	public function pid(){
		return self::ENCRYPTION_REQUEST_PACKET;
	}

	protected function encode(){
		$this->putString($this->serverID);
		$this->putVarInt(strlen($this->publicKey));
		$this->put($this->publicKey);
		$this->putVarInt(strlen($this->verifyToken));
		$this->put($this->verifyToken);
	}
}
