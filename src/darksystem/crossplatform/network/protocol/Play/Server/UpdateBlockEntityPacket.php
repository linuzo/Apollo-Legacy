<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use darksystem\crossplatform\utils\ConvertUtils;
use pocketmine\nbt\tag\Tag;

class UpdateBlockEntityPacket extends OutboundPacket{

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $actionID;
	/** @var Tag */
	public $namedtag;

	public function pid(){
		return self::UPDATE_BLOCK_ENTITY_PACKET;
	}

	protected function encode(){
		$this->putPosition($this->x, $this->y, $this->z);
		$this->putByte($this->actionID);
		$this->put(ConvertUtils::convertNBTDataFromPEtoPC($this->namedtag));
	}
}
