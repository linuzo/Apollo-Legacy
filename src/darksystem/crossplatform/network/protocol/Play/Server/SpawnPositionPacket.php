<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class SpawnPositionPacket extends OutboundPacket{

	/** @var int */
	public $spawnX;
	/** @var int */
	public $spawnY;
	/** @var int */
	public $spawnZ;

	public function pid(){
		return self::SPAWN_POSITION_PACKET;
	}

	protected function encode(){
		$this->putPosition($this->spawnX, $this->spawnY, $this->spawnZ);
	}
}