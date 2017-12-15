<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class JoinGamePacket extends OutboundPacket{

	/** @var int */
	public $eid;
	/** @var int */
	public $gamemode;
	/** @var int */
	public $dimension;
	/** @var int */
	public $difficulty;
	/** @var int */
	public $maxPlayers;
	/** @var string */
	public $levelType;
	/** @var bool */
	public $reducedDebugInfo = false;

	public function pid(){
		return self::JOIN_GAME_PACKET;
	}

	protected function encode(){
		$this->putInt($this->eid);
		$this->putByte($this->gamemode);
		$this->putInt($this->dimension);
		$this->putByte($this->difficulty);
		$this->putByte($this->maxPlayers);
		$this->putString($this->levelType);
		$this->putBool($this->reducedDebugInfo);
	}
}
