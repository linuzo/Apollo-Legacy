<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class EntityActionPacket extends InboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $actionID;
	/** @var int */
	public $jumpboost;

	public function pid(){
		return self::ENTITY_ACTION_PACKET;
	}

	protected function decode(){
		$this->eid = $this->getVarInt();
		$this->actionID = $this->getVarInt();
		$this->jumpboost = $this->getVarInt();
	}
}