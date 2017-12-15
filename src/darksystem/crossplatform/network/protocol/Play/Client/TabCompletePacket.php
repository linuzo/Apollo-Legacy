<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class TabCompletePacket extends InboundPacket{

	/** @var string */
	public $text;
	/** @var bool */
	public $assumeCommand;
	/** @var bool */
	public $hasPosition;
	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;

	public function pid(){
		return self::TAB_COMPLETE_PACKET;
	}

	protected function decode(){
		$this->text = $this->getString();
		$this->assumeCommand = $this->getBool();
		$this->hasPosition = $this->getBool();
		if($this->hasPosition){
			$this->getPosition($this->x, $this->y, $this->z);
		}
	}
}
