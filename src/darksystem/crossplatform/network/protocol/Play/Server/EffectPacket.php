<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EffectPacket extends OutboundPacket{

	/** @var int */
	public $effectId;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $data;
	/** @var bool */
	public $disableRelativeVolume;

	public function pid(){
		return self::EFFECT_PACKET;
	}

	protected function encode(){
		$this->putInt($this->effectId);
		$this->putPosition($this->x, $this->y, $this->z);
		$this->putInt($this->data);
		$this->putBool($this->disableRelativeVolume);
	}
}
