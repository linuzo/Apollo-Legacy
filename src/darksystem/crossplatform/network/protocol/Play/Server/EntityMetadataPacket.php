<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use darksystem\crossplatform\utils\Binary;

class EntityMetadataPacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var array */
	public $metadata;

	public function pid(){
		return self::ENTITY_METADATA_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->eid);
		$this->put(Binary::writeMetadata($this->metadata));
	}
}