<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class PlayerAbilitiesPacket extends OutboundPacket{

	/** @var bool */
	public $damageDisabled;
	/** @var bool */
	public $canFly;
	/** @var bool */
	public $isFlying = false;
	/** @var bool */
	public $isCreative;

	/** @var float */
	public $flyingSpeed;
	/** @var float */
	public $walkingSpeed;

	public function pid(){
		return self::PLAYER_ABILITIES_PACKET;
	}

	protected function encode(){
		$flags = 0;
		if($this->isCreative){
			$flags |= 0b1;
		}
		if($this->isFlying){
			$flags |= 0b10;
		}
		if($this->canFly){
			$flags |= 0b100;
		}
		if($this->damageDisabled){
			$flags |= 0b1000;
		}
		$this->putByte($flags);
		$this->putFloat($this->flyingSpeed);
		$this->putFloat($this->walkingSpeed);
	}
}
