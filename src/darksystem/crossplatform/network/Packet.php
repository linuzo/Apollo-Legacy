<?php

namespace darksystem\crossplatform\network;

use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use darksystem\crossplatform\utils\Binary;
use darksystem\crossplatform\utils\ConvertUtils;
use darksystem\crossplatform\utils\ComputerItem;

abstract class Packet extends \stdClass{

	/** @var string */
	protected $buffer;
	/** @var int */
	protected $offset = 0;

	protected function get($len){
		if($len < 0){
			$this->offset = strlen($this->buffer) - 1;

			return "";
		}elseif($len === true){
			return substr($this->buffer, $this->offset);
		}

		$buffer = "";
		for(; $len > 0; --$len, ++$this->offset){
			$buffer .= @$this->buffer{$this->offset};
		}

		return $buffer;
	}

	protected function getLong(){
		return Binary::readLong($this->get(8));
	}

	protected function getInt(){
		return Binary::readInt($this->get(4));
	}

	protected function getPosition(&$x=null, &$y=null, &$z=null){
		$long = $this->getLong();
		$x = $long >> 38;
		$y = ($long >> 26) & 0xFFF;
		$z = $long << 38 >> 38;
	}

	protected function getFloat(){
		return Binary::readFloat($this->get(4));
	}

	protected function getDouble(){
		return Binary::readDouble($this->get(8));
	}

	/**
	 * @return Item
	 */
	protected function getSlot(){
		$itemId = $this->getSignedShort();
		if($itemId === -1){ //Empty
			return Item::get(Item::AIR, 0, 0);
		}else{
			$count = $this->getSignedByte();
			$damage = $this->getSignedShort();
			$nbt = $this->get(true);

			$nbt = ConvertUtils::convertNBTDataFromPCtoPE($nbt);
			$item = new ComputerItem($itemId, $damage, $count, $nbt);

			ConvertUtils::convertItemData(false, $item);

			return $item;
		}
	}

	protected function putSlot(Item $item){
		ConvertUtils::convertItemData(true, $item);

		if($item->getID() === 0){
			$this->putShort(-1);
		}else{
			$this->putShort($item->getID());
			$this->putByte($item->getCount());
			$this->putShort($item->getDamage());

			$nbt = new NBT(NBT::LITTLE_ENDIAN);
			$nbt->read($item->getCompoundTag());
			$nbt = $nbt->getData();

			$this->put(ConvertUtils::convertNBTDataFromPEtoPC($nbt));
		}
	}

	protected function getShort(){
		return Binary::readShort($this->get(2));
	}

	protected function getSignedShort(){
		return Binary::readSignedShort($this->get(2));
	}

	protected function getTriad(){
		return Binary::readTriad($this->get(3));
	}

	protected function getLTriad(){
		return Binary::readTriad(strrev($this->get(3)));
	}

	protected function getBool(){
		return $this->get(1) !== "\x00";
	}

	protected function getByte(){
		return ord($this->buffer{$this->offset++});
	}

	protected function getSignedByte(){
		return ord($this->buffer{$this->offset++}) << 56 >> 56;
	}

	protected function getAngle(){
		return $this->getByte() * 360 / 256;
	}

	protected function getString(){
		return $this->get($this->getVarInt());
	}

	protected function getVarInt(){
		return Binary::readComputerVarInt($this->buffer, $this->offset);
	}

	protected function feof(){
		return !isset($this->buffer{$this->offset});
	}

	protected function put($str){
		$this->buffer .= $str;
	}

	protected function putLong($v){
		$this->buffer .= Binary::writeLong($v);
	}

	protected function putInt($v){
		$this->buffer .= Binary::writeInt($v);
	}

	protected function putPosition($x, $y, $z){
		$long = (($x & 0x3FFFFFF) << 38) | (($y & 0xFFF) << 26) | ($z & 0x3FFFFFF);
		$this->putLong($long);
	}

	protected function putFloat($v){
		$this->buffer .= Binary::writeFloat($v);
	}

	protected function putDouble($v){
		$this->buffer .= Binary::writeDouble($v);
	}

	protected function putShort($v){
		$this->buffer .= Binary::writeShort($v);
	}

	protected function putTriad($v){
		$this->buffer .= Binary::writeTriad($v);
	}

	protected function putLTriad($v){
		$this->buffer .= strrev(Binary::writeTriad($v));
	}

	protected function putBool($v){
		$this->buffer .= ($v ? "\x01" : "\x00");
	}

	protected function putByte($v){
		$this->buffer .= chr($v);
	}

	/**
	 * @param float $v any number is valid, including negative numbers and numbers greater than 360
	 */
	protected function putAngle($v){
		$this->putByte((int) round($v * 256 / 360));
	}

	protected function putString($v){
		$this->putVarInt(strlen($v));
		$this->put($v);
	}

	protected function putVarInt($v){
		$this->buffer .= Binary::writeComputerVarInt($v);
	}

	public abstract function pid();

	protected abstract function encode();

	protected abstract function decode();

	public function write(){
		$this->buffer = "";
		$this->offset = 0;
		$this->encode();
		return Binary::writeComputerVarInt($this->pid()) . $this->buffer;
	}

	public function read($buffer, $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
		$this->decode();
	}
}
