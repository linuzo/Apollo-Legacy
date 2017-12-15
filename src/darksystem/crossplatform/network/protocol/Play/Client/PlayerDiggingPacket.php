<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PlayerDiggingPacket extends InboundPacket{

	/** @var int */
	public $status;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $face;

	public function pid(){
		return self::PLAYER_DIGGING_PACKET;
	}

	protected function decode(){
		$this->status = $this->getVarInt();
		$this->getPosition($this->x, $this->y, $this->z);
		$this->face = $this->getSignedByte();
	}
}
