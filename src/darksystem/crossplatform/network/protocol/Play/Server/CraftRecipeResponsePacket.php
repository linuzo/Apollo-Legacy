<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class CraftRecipeResponsePacket extends OutboundPacket{

	/** @var int */
	public $windowID;
	/** @var int */
	public $recipeId = -1;

	public function pid(){
		return self::CRAFT_RECIPE_RESPONSE_PACKET;
	}

	protected function encode(){
		$this->putByte($this->windowID);
		$this->putVarInt($this->recipeId);
	}
}