<?php

namespace darksystem\crossplatform\utils;

use pocketmine\network\protocol\CraftingDataPacket;

use darksystem\crossplatform\DesktopPlayer;

class RecipeUtils{

	/** @var DesktopPlayer */
	private $player;

	public function __construct(DesktopPlayer $player){
		$this->player = $player;
	}

	public function onCraftingData(CraftingDataPacket $packet){
		return null;
	}

	public function __a(){
		/*$pk = new UnlockRecipesPacket();
		$pk->actionID = 0;
		$pk->recipes[] = 163;
		$pk->recipes[] = 438;
		$pk->recipes[] = 424;
		$pk->extraRecipes[] = 0;
		$this->putRawPacket($pk);*/
	}

}
