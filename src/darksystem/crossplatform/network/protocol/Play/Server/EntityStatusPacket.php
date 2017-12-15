<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityStatusPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $status;

	public function pid(){
		return self::ENTITY_STATUS_PACKET;
	}

	protected function encode(){
		$this->putInt($this->eid);
		$this->putByte($this->status);
	}
}