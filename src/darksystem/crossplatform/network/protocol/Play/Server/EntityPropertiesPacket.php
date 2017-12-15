<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class EntityPropertiesPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var array */
	public $entries = [];

	public function pid(){
		return self::ENTITY_PROPERTIES_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->putInt(count($this->entries));
		foreach($this->entries as $entry){
			$this->putString($entry[0]);
			$this->putDouble($entry[1]);
			$this->putVarInt(0);
		}
	}
}
