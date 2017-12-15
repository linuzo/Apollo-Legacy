<?php

namespace darksystem\crossplatform;

use pocketmine\block\Block;
use pocketmine\level\format\LevelProvider;
use darksystem\crossplatform\utils\Binary;
use darksystem\crossplatform\utils\ConvertUtils;
use darksystem\crossplatform\entity\ItemFrameBlockEntity;

class DesktopChunk{
	
	/** @var DesktopPlayer */
	private $player;
	/** @var int */
	private $chunkX;
	/** @var int */
	private $chunkZ;
	/** @var LevelProvider */
	private $provider;
	/** @var bool */
	private $groundUp;
	/** @var int */
	private $bitMap;
	/** @var string */
	private $biomes;
	/** @var string */
	private $chunkData;

	/**
	 * @param DesktopPlayer $player
	 * @param int           $chunkX
	 * @param int           $chunkZ
	 */
	public function __construct(DesktopPlayer $player, $chunkX, $chunkZ){
		$this->player = $player;
		$this->chunkX = $chunkX;
		$this->chunkZ = $chunkZ;
		$this->provider = $player->getLevel()->getProvider();
		$this->groundUp = true;
		$this->bitMap = 0;

		$this->generateChunk();
	}

	public function generateChunk(){
		$chunk = $this->provider->getChunk($this->chunkX, $this->chunkZ, false);
		$this->biomes = $chunk->getBiomeIdArray();

		$payload = "";
		foreach($chunk->getSubChunks() as $num => $subChunk){
			if($subChunk->isEmpty()){
				continue;
			}

			$this->bitMap |= 0x01 << $num;

			$palette = [];
			$bitsperblock = 8;

			$chunkdata = "";
			for($y = 0; $y < 16; ++$y){
				for($z = 0; $z < 16; ++$z){

					$data = "";
					for($x = 0; $x < 16; ++$x){
						$blockid = $subChunk->getBlockId($x, $y, $z);
						$blockdata = $subChunk->getBlockData($x, $y, $z);

						if($blockid == Block::FRAME_BLOCK){
							ItemFrameBlockEntity::getItemFrame($this->player->getLevel(), $x + ($this->chunkX << 4), $y + ($num << 4), $z + ($this->chunkZ << 4), $blockdata, true);
							$block = Block::AIR;
						}else{
							ConvertUtils::convertBlockData(true, $blockid, $blockdata);
							$block = (int) ($blockid << 4) | $blockdata;
						}

						if(($key = array_search($block, $palette, true)) === false){
							$key = count($palette);
							$palette[$key] = $block;
						}
						$data .= chr($key);

						if($x === 7 or $x === 15){
							$chunkdata .= strrev($data);
							$data = "";
						}
					}
				}
			}

			$blocklightdata = "";
			$skylightdata = "";
			for($y = 0; $y < 16; ++$y){
				for($z = 0; $z < 16; ++$z){
					for($x = 0; $x < 16; $x += 2){
						$blocklight = $subChunk->getBlockLight($x, $y, $z) | ($subChunk->getBlockLight($x + 1, $y, $z) << 4);
						$skylight = $subChunk->getBlockSkyLight($x, $y, $z) | ($subChunk->getBlockSkyLight($x + 1, $y, $z) << 4);

						$blocklightdata .= chr($blocklight);
						$skylightdata .= chr($skylight);
					}
				}
			}
			
			$payload .= Binary::writeByte($bitsperblock).Binary::writeComputerVarInt(count($palette));
			
			foreach($palette as $value){
				$payload .= Binary::writeComputerVarInt($value);
			}
			
			$payload .= Binary::writeComputerVarInt(strlen($chunkdata) / 8);
			
			$payload .= $chunkdata;
			
			$payload .= $blocklightdata;
			
			if($this->player->bigBrother_getDimension() === 0){
				$payload .= $skylightdata;
			}
		}

		$this->chunkData = $payload;
	}

	/**
	 * @return bool
	 */
	public function isGroundUp(){
		return $this->groundUp;
	}

	/**
	 * @return int
	 */
	public function getBitMapData(){
		return $this->bitMap;
	}

	/**
	 * @return string
	 */
	public function getBiomesData(){
		return $this->biomes;
	}

	/**
	 * @return string
	 */
	public function getChunkData(){
		return $this->chunkData;
	}
}
