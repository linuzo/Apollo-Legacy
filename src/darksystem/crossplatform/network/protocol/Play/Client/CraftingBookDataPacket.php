<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class CraftingBookDataPacket extends InboundPacket{

	/** @var int */
	public $type;
	/** @var int */
	public $recipeId = -1;
	/** @var bool */
	public $isCraftingBookOpen = false;
	/** @var bool */
	public $isFilteringCraftable = false;

	public function pid(){
		return self::CRAFTING_BOOK_DATA_PACKET;
	}

	protected function decode(){
		$this->type = $this->getVarInt();
		switch($this->type){
			case 0:
				$this->recipeId = $this->getInt();
			break;
			case 1:
				$this->isCraftingBookOpen = $this->getBool();
				$this->isFilteringCraftable = $this->getBool();
			break;
		}
	}
}
