<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class BlockActionPacket extends OutboundPacket{

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $actionID;
	/** @var int */
	public $actionParam;
	/** @var int */
	public $blockType;

	public function pid(){
		return self::BLOCK_ACTION_PACKET;
	}

	protected function encode(){
		$this->putPosition($this->x, $this->y, $this->z);
		$this->putByte($this->actionID);
		$this->putByte($this->actionParam);
		$this->putVarInt($this->blockType);
	}
}
