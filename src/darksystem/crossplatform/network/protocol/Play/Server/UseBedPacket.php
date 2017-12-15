<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class UseBedPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $bedX;
	/** @var int */
	public $bedY;
	/** @var int */
	public $bedZ;

	public function pid(){
		return self::USE_BED_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putPosition($this->bedX, $this->bedY, $this->bedZ);
	}
}