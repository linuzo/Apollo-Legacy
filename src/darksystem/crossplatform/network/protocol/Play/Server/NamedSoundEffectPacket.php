<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class NamedSoundEffectPacket extends OutboundPacket{

	/** @var string */
	public $name;
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
		return self::NAMED_SOUND_EFFECT_PACKET;
	}

	protected function encode(){
		$this->putString($this->name);
		$this->putVarInt($this->category);
		$this->putInt($this->x * 8);
		$this->putInt($this->y * 8);
		$this->putInt($this->z * 8);
		$this->putFloat($this->volume);
		$this->putFloat($this->pitch);
	}
}
