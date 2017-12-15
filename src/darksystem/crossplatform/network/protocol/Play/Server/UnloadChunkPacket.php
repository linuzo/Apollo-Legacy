<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class UnloadChunkPacket extends OutboundPacket{

	/** @var int */
	public $chunkX;
	/** @var int */
	public $chunkZ;

	public function pid(){
		return self::UNLOAD_CHUNK_PACKET;
	}

	protected function encode(){
		$this->putInt($this->chunkX);
		$this->putInt($this->chunkZ);
	}
}