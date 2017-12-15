<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class ConfirmTransactionPacket extends InboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $actionNumber;
	/** @var bool */
	public $accepted;

	public function pid(){
		return self::CONFIRM_TRANSACTION_PACKET;
	}

	protected function decode(){
		$this->windowID = $this->getSignedByte();
		$this->actionNumber = $this->getSignedShort();
		$this->accepted = $this->getBool();
	}
}