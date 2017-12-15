<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class AdvancementTabPacket extends InboundPacket{

	/** @var int  */
	public $status;
	public $tabId;

	public function pid(){
		return self::ADVANCEMENT_TAB_PACKET;
	}

	protected function decode(){
		$this->status = $this->getVarInt();
		if($this->status === 0){
			$this->tabId = $this->getString();
		}
	}
}