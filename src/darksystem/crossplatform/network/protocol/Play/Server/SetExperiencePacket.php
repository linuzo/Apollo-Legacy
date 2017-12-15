<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class SetExperiencePacket extends OutboundPacket{

	/** @var float */
	public $experience;
	/** @var int */
	public $level;
	/** @var int */
	public $totalexperience;

	public function pid(){
		return self::SET_EXPERIENCE_PACKET;
	}

	protected function encode(){
		$this->putFloat($this->experience);
		$this->putVarInt($this->level);
		$this->putVarInt($this->totalexperience);
	}
}