<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PlayerBlockPlacementPacket extends InboundPacket{

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $direction;
	/** @var int */
	public $hand;
	/** @var float */
	public $cursorX;
	/** @var float */
	public $cursorY;
	/** @var float */
	public $cursorZ;

	public function pid(){
		return self::PLAYER_BLOCK_PLACEMENT_PACKET;
	}

	protected function decode(){
		$this->getPosition($this->x, $this->y, $this->z);
		$this->direction = $this->getVarInt();
		$this->hand = $this->getVarInt();
		$this->cursorX = $this->getFloat();
		$this->cursorY = $this->getFloat();
		$this->cursorZ = $this->getFloat();
	}
}
