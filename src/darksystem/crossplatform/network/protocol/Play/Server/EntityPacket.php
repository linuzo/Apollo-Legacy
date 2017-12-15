<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityPacket extends OutboundPacket{

	/** @var int */
	public $eid;

	public function pid(){
		return self::ENTITY_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
	}
}