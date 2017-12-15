<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class RemoveEntityEffectPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $effectId;

	public function pid(){
		return self::REMOVE_ENTITY_EFFECT_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putByte($this->effectId);
	}
}