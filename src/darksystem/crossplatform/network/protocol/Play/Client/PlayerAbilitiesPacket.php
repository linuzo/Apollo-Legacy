<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PlayerAbilitiesPacket extends InboundPacket{

	/** @var bool */
	public $damageDisabled = false;
	/** @var bool */
	public $canFly = false;
	/** @var bool */
	public $isFlying = false;
	/** @var bool */
	public $isCreative = false;

	/** @var float */
	public $flyingSpeed;
	/** @var float */
	public $walkingSpeed;

	public function pid(){
		return self::PLAYER_ABILITIES_PACKET;
	}

	protected function decode(){
		$flags = $this->getSignedByte();

		$this->damageDisabled = ($flags & 0x08) !== 0;
		$this->canFly = ($flags & 0x04) !== 0;
		$this->isFlying = ($flags & 0x02) !== 0;
		$this->isCreative = ($flags & 0x01) !== 0;

		$this->flyingSpeed = $this->getFloat();
		$this->walkingSpeed = $this->getFloat();
	}
}
