<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class TimeUpdatePacket extends OutboundPacket{

	/** @var int */
	public $age;
	/** @var int */
	public $time;

	public function pid(){
		return self::TIME_UPDATE_PACKET;
	}

	protected function encode(){
		$this->putLong($this->age);
		$this->putLong($this->time);
	}
}