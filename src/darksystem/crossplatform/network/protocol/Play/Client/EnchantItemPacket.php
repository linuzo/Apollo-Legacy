<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class EnchantItemPacket extends InboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $enchantment;

	public function pid(){
		return self::ENCHANT_ITEM_PACKET;
	}

	protected function decode(){
		$this->windowID = $this->getSignedByte();
		$this->enchantment = $this->getSignedByte();
	}
}