<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class TabCompletePacket extends OutboundPacket{

	/** @var string[] */
	public $matches = [];

	public function pid(){
		return self::TAB_COMPLETE_PACKET;
	}

	protected function encode(){
		$this->putVarInt(count($this->matches));
		foreach($this->matches as $match){
			$this->putString($match);
		}
	}
}