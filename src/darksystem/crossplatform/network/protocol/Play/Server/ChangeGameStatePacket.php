<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class ChangeGameStatePacket extends OutboundPacket{

	/** @var int */
	public $reason;
	/** @var float */
	public $value;

	public function pid(){
		return self::CHANGE_GAME_STATE_PACKET;
	}

	protected function encode(){
		$this->putByte($this->reason);
		$this->putFloat($this->value);
	}
}