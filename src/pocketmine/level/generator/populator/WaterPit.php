<?php


namespace pocketmine\level\generator\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use pocketmine\level\generator\populator\VariableAmountPopulator;

class WaterPit extends VariableAmountPopulator{
	/** @var ChunkManager */
	private $level;

	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		$this->level = $level;
		$amount = $this->getAmount($random);
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);

			if($y !== -1 and $this->canWaterPitStay($x, $y, $z)){
				$this->level->setBlockIdAt($x, $y, $z, BlockFactory::WATER);
				$this->level->setBlockDataAt($x, $y, $z , 8);
			}
		}
	}

	private function canWaterPitStay($x, $y, $z){
		$b = $this->level->getBlockIdAt($x, $y, $z);
		return ($b === BlockFactory::AIR or $b === BlockFactory::DIRT) and $this->level->getBlockIdAt($x, $y, $z) === DIRT::DIRT;
	}

	private function getHighestWorkableBlock($x, $z){
		for($y = 61; $y >= 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b !== BlockFactory::AIR and $b !== BlockFactory::LEAVES and $b !== BlockFactory::LEAVES2 and $b !== BlockFactory::SNOW_LAYER){
				break;
			}
		}

		return $y === 0 ? -1 : ++$y;
	}
}
