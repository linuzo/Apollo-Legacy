<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class TitlePacket extends OutboundPacket{

	const TYPE_SET_TITLE = 0;
	const TYPE_SET_SUB_TITLE = 1;
	const TYPE_SET_ACTION_BAR = 2;
	const TYPE_SET_SETTINGS = 3;
	const TYPE_HIDE = 4;
	const TYPE_RESET = 5;

	/** @var int */
	public $actionID;
	/** @var string|int[] */
	public $data = null;

	public function pid(){
		return self::TITLE_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->actionID);
		switch($this->actionID){
			case self::TYPE_SET_TITLE:
			case self::TYPE_SET_SUB_TITLE:
			case self::TYPE_SET_ACTION_BAR:
				$this->putString($this->data);
			break;
			case self::TYPE_SET_SETTINGS:
				$this->putInt($this->data[0]);
				$this->putInt($this->data[1]);
				$this->putInt($this->data[2]);
			break;
		}
	}
}
