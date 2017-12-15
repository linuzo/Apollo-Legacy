<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class BossBarPacket extends OutboundPacket{

	const TYPE_ADD = 0;
	const TYPE_REMOVE = 1;
	const TYPE_UPDATE_HEALTH = 2;
	const TYPE_UPDATE_TITLE = 3;
	const TYPE_UPDATE_COLOR = 4;
	const TYPE_UPDATE_FLAGS = 5;

	const COLOR_PINK = 0;
	const COLOR_BLUE = 1;
	const COLOR_RED = 2;
	const COLOR_GREEN = 3;
	const COLOR_YELLOW = 4;
	const COLOR_PURPLE = 5;
	const COLOR_WHITE = 6;

	const DIVISION_ZERO = 0;
	const DIVISION_SIX = 1;
	const DIVISION_TEN = 2;
	const DIVISION_TWELVE = 3;
	const DIVISION_TWENTY = 4;

	const FLAG_DARK_SKY = 0x01;
	const FLAG_DRAGON_BAR = 0x02; //also used to play end music

	/** @var string */
	public $uuid;
	/** @var int */
	public $actionID;
	/** @var string */
	public $title;
	/** @var float */
	public $health = 1;
	/** @var int */
	public $color = self::COLOR_PURPLE;
	/** @var int */
	public $division = self::DIVISION_ZERO;
	/** @var int */
	public $flags = 0;

	public function pid(){
		return self::BOSS_BAR_PACKET;
	}

	protected function encode(){
		$this->put($this->uuid);
		$this->putVarInt($this->actionID);
		switch($this->actionID){
			case self::TYPE_ADD:
				$this->putString($this->title);
				$this->putFloat($this->health);
				$this->putVarInt($this->color);
				$this->putVarInt($this->division);
				$this->putByte($this->flags);
			break;
			case self::TYPE_REMOVE:
			break;
			case self::TYPE_UPDATE_HEALTH:
				$this->putFloat($this->health);
			break;
			case self::TYPE_UPDATE_TITLE:
				$this->putString($this->title);
			break;
			case self::TYPE_UPDATE_COLOR:
				$this->putVarInt($this->color);
			break;
			case self::TYPE_UPDATE_FLAGS:
				$this->putByte($this->flags);
			break;
		}
	}
}
