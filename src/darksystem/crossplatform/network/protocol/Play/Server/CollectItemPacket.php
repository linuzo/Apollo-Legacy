<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class CollectItemPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $target;
	/** @var int */
	public $itemCount;

	public function pid(){
		return self::COLLECT_ITEM_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->target);
		$this->putVarInt($this->eid);
		$this->putVarInt($this->itemCount);
	}
}