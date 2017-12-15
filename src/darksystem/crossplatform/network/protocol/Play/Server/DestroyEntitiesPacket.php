<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class DestroyEntitiesPacket extends OutboundPacket{

	/** @var int[] */
	public $ids = [];

	public function pid(){
		return self::DESTROY_ENTITIES_PACKET;
	}

	protected function encode(){
		$this->putVarInt(count($this->ids));
		foreach($this->ids as $id){
			$this->putVarInt($id);
		}
	}
}