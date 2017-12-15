<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class UseEntityPacket extends InboundPacket{

	const INTERACT = 0;
	const ATTACK = 1;
	const INTERACT_AT = 2;

	/** @var int */
	public $target;
	/** @var int */
	public $type;

	/** @var float */
	public $targetX;
	/** @var float */
	public $targetY;
	/** @var float */
	public $targetZ;

	/** @var int */
	public $hand;

	public function pid(){
		return self::USE_ENTITY_PACKET;
	}

	protected function decode(){
		$this->target = $this->getVarInt();
		$this->type = $this->getVarInt();
		if($this->type === self::INTERACT_AT){
			$this->targetX = $this->getFloat();
			$this->targetY = $this->getFloat();
			$this->targetZ = $this->getFloat();
		}
		if($this->type !== self::ATTACK){
			$this->hand = $this->getVarInt();
		}
	}
}
