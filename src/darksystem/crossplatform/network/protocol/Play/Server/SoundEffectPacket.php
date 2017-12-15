<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class SoundEffectPacket extends OutboundPacket{

	/** @var int */
	public $id;
	/** @var int */
	public $category;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var float */
	public $volume;
	/** @var float */
	public $pitch;

	public function pid(){
		return self::SOUND_EFFECT_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->id);
		$this->putVarInt($this->category);
		$this->putInt($this->x * 8);
		$this->putInt($this->y * 8);
		$this->putInt($this->z * 8);
		$this->putFloat($this->volume);
		$this->putFloat($this->pitch);
	}
}
