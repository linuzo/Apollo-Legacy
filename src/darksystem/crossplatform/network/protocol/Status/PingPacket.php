<?php

namespace darksystem\crossplatform\network\protocol\Status;

use darksystem\crossplatform\network\Packet;

class PingPacket extends Packet{

	/** @var int */
	public $time;

	public function pid(){
		return 0x01;
	}

	protected function encode(){
		$this->putLong($this->time);
	}

	protected function decode(){
		$this->time = $this->getLong();
	}
}