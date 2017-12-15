<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class RespawnPacket extends OutboundPacket{

	/** @var int */
	public $dimension;
	/** @var int */
	public $difficulty;
	/** @var int */
	public $gamemode;
	/** @var string */
	public $levelType;

	public function pid(){
		return self::RESPAWN_PACKET;
	}

	protected function encode(){
		$this->putInt($this->dimension);
		$this->putByte($this->difficulty);
		$this->putByte($this->gamemode);
		$this->putString($this->levelType);
	}
}
