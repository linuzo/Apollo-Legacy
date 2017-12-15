<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityEffectPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $effectId;
	/** @var int */
	public $amplifier;
	/** @var int */
	public $duration;
	/** @var int */
	public $flags;

	public function pid(){
		return self::ENTITY_EFFECT_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putByte($this->effectId);
		$this->putByte($this->amplifier);
		$this->putVarInt($this->duration);
		$this->putByte($this->flags);
	}
}
