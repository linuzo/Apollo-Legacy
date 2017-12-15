<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;
use darksystem\crossplatform\utils\ConvertUtils;
use darksystem\crossplatform\CrossPlatform;
use pocketmine\tile\Tile;

class ChunkDataPacket extends OutboundPacket{

	/** @var int */
	public $chunkX;
	/** @var int */
	public $chunkZ;
	/** @var bool */
	public $groundUp;
	/** @var int */
	public $primaryBitmap;
	/** @var string */
	public $payload;
	/** @var string */
	public $biomes;
	/** @var array */
	public $blockEntities = [];

	public function pid(){
		return self::CHUNK_DATA_PACKET;
	}

	protected function encode(){
		$this->putInt($this->chunkX);
		$this->putInt($this->chunkZ);
		$this->putBool($this->groundUp);
		$this->putVarInt($this->primaryBitmap);
		if($this->groundUp){
			$this->putVarInt(strlen($this->payload.$this->biomes));
			$this->put($this->payload);
			$this->put($this->biomes);
		}else{
			$this->putVarInt(strlen($this->payload));
			$this->put($this->payload);
		}
		$this->putVarInt(count($this->blockEntities));
		foreach($this->blockEntities as $blockEntity){
			switch($blockEntity->id){
				case Tile::FLOWER_POT:
					$blockEntity->Item = clone $blockEntity->item;
					$blockEntity->Item->setName("Item");
					unset($blockEntity->item);

					$blockEntity->Data = clone $blockEntity->mData;
					$blockEntity->Data->setName("Data");
					unset($blockEntity->mData);
				break;
				case Tile::SIGN:
					$textData = explode("\n", $blockEntity->Text->getValue());
					
					$blockEntity->Text1 = clone $blockEntity->Text;
					$blockEntity->Text1->setName("Text1");
					$blockEntity->Text1->setValue(CrossPlatform::toJSON($textData[0]));

					$blockEntity->Text2 = clone $blockEntity->Text;
					$blockEntity->Text2->setName("Text2");
					$blockEntity->Text2->setValue(CrossPlatform::toJSON($textData[1]));

					$blockEntity->Text3 = clone $blockEntity->Text;
					$blockEntity->Text3->setName("Text3");
					$blockEntity->Text3->setValue(CrossPlatform::toJSON($textData[2]));

					$blockEntity->Text4 = clone $blockEntity->Text;
					$blockEntity->Text4->setName("Text4");
					$blockEntity->Text4->setValue(CrossPlatform::toJSON($textData[3]));
					unset($blockEntity->Text);
				break;
			}

			$this->put(ConvertUtils::convertNBTDataFromPEtoPC($blockEntity));
		}
	}
}
