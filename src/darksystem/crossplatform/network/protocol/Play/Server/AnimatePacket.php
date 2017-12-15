<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class AnimatePacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $actionID;

	public function pid(){
		return self::ANIMATE_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putByte($this->actionID);
	}
}