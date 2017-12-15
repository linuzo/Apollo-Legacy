<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class ClientStatusPacket extends InboundPacket{

	/** @var int */
	public $actionID;

	public function pid(){
		return self::CLIENT_STATUS_PACKET;
	}

	protected function decode(){
		$this->actionID = $this->getVarInt();
	}
}