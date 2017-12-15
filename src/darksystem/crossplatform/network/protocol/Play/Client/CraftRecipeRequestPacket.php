<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class CraftRecipeRequestPacket extends InboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $recipeId = -1;
	/** @var bool */
	public $isMakeAll = false;

	public function pid(){
		return self::CRAFT_RECIPE_REQUEST_PACKET;
	}

	protected function decode(){
		$this->windowID = $this->getSignedByte();
		$this->recipeId = $this->getVarInt();
		$this->isMakeAll = $this->getBool();
	}
}