<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class BlockBreakAnimationPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $destroyStage;

	public function pid(){
		return self::BLOCK_BREAK_ANIMATION_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putPosition($this->x, $this->y, $this->z);
		$this->putByte($this->destroyStage);
	}
}
