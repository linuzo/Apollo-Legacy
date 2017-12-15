<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class UpdateSignPacket extends InboundPacket{

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var string */
	public $line1;
	/** @var string */
	public $line2;
	/** @var string */
	public $line3;
	/** @var string */
	public $line4;

	public function pid(){
		return self::UPDATE_SIGN_PACKET;
	}

	protected function decode(){
		$this->getPosition($this->x, $this->y, $this->z);
		$this->line1 = $this->getString();
		$this->line2 = $this->getString();
		$this->line3 = $this->getString();
		$this->line4 = $this->getString();
	}
}
