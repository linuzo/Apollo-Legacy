<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class ServerDifficultyPacket extends OutboundPacket{

	/** @var int */
	public $difficulty;

	public function pid(){
		return self::SERVER_DIFFICULTY_PACKET;
	}

	protected function encode(){
		$this->putByte($this->difficulty);
	}
}