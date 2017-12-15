<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class SelectAdvancementTabPacket extends OutboundPacket{

	/** @var bool */
	public $hasTab = false;
	/** @var string */
	public $tabId = "";

	public function pid(){
		return self::SELECT_ADVANCEMENT_TAB_PACKET;
	}

	protected function encode(){
		$this->putBool($this->hasTab);
		if($this->hasTab){
			$this->putString($this->tabId);
		}
	}
}