<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class ConfirmTransactionPacket extends OutboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $actionNumber;
	/** @var bool */
	public $accepted;

	public function pid(){
		return self::CONFIRM_TRANSACTION_PACKET;
	}

	protected function encode(){
		$this->putByte($this->windowID);
		$this->putShort($this->actionNumber);
		$this->putBool($this->accepted);
	}
}