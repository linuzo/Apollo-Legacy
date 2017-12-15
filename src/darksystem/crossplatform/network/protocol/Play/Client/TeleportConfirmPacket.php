<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class TeleportConfirmPacket extends InboundPacket{

	/** @var int */
	public $teleportId;

	public function pid(){
		return self::TELEPORT_CONFIRM_PACKET;
	}

	protected function decode(){
		$this->teleportId = $this->getVarInt();
	}
}