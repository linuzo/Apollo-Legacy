<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class BlockChangePacket extends OutboundPacket{

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $blockId;
	/** @var int */
	public $blockMeta;

	public function pid(){
		return self::BLOCK_CHANGE_PACKET;
	}

	protected function encode(){
		$this->putPosition($this->x, $this->y, $this->z);
		$this->putVarInt(($this->blockId << 4) | ($this->blockMeta & 15));
	}
}
