<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class OpenSignEditorPacket extends OutboundPacket{

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;

	public function pid(){
		return self::OPEN_SIGN_EDITOR_PACKET;
	}

	protected function encode(){
		$this->putPosition($this->x, $this->y, $this->z);
	}
}