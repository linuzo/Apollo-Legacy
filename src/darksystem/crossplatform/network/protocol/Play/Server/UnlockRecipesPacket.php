<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class UnlockRecipesPacket extends OutboundPacket{

	/** @var int */
	public $actionID;
	/** @var bool */
	public $isCraftingBookOpen = false;
	/** @var bool */
	public $isFilteringCraftable = false;
	/** @var int[] */
	public $recipes = [];
	/** @var int[] */
	public $extraRecipes = [];

	public function pid(){
		return self::UNLOCK_RECIPES_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->actionID);
		$this->putBool($this->isCraftingBookOpen);
		$this->putBool($this->isFilteringCraftable);
		$this->putVarInt(count($this->recipes));
		foreach($this->recipes as $recipeId){
			$this->putVarInt($recipeId);
		}
		if($this->actionID === 0){
			$this->putVarInt(count($this->extraRecipes));
			foreach($this->extraRecipes as $recipeId){
				$this->putVarInt($recipeId);
			}
		}
	}
}
