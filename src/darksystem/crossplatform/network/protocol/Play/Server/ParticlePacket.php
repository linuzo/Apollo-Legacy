<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class ParticlePacket extends OutboundPacket{

	/** @var int */
	public $id;
	/** @var bool */
	public $longDistance = false;
	/** @var float */
	public $x;
	/** @var float */
	public $y;
	/** @var float */
	public $z;
	/** @var float */
	public $offsetX;
	/** @var float */
	public $offsetY;
	/** @var float */
	public $offsetZ;
	/** @var float */
	public $data;
	/** @var int */
	public $count;
	/** @var array */
	public $addData = [];

	public function pid(){
		return self::PARTICLE_PACKET;
	}

	protected function encode(){
		$this->putInt($this->id);
		$this->putBool($this->longDistance);
		$this->putFloat($this->x);
		$this->putFloat($this->y);
		$this->putFloat($this->z);
		$this->putFloat($this->offsetX);
		$this->putFloat($this->offsetY);
		$this->putFloat($this->offsetZ);
		$this->putFloat($this->data);
		$this->putInt($this->count);
		foreach($this->addData as $addData){
			$this->putVarInt($addData);
		}
	}
}
