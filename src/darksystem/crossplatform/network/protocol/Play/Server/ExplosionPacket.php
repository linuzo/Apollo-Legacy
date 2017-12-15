<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class ExplosionPacket extends OutboundPacket{

	/** @var float */
	public $x;
	/** @var float */
	public $y;
	/** @var float */
	public $z;
	/** @var float */
	public $radius;
	/** @var array */
	public $records = [];
	/** @var float */
	public $motionX;
	/** @var float */
	public $motionY;
	/** @var float */
	public $motionZ;

	public function pid(){
		return self::EXPLOSION_PACKET;
	}

	protected function encode(){
		$this->putFloat($this->x);
		$this->putFloat($this->y);
		$this->putFloat($this->z);
		$this->putFloat($this->radius);
		$this->putInt(count($this->records));
		foreach($this->records as $record){
			$this->putByte($record->getX());
			$this->putByte($record->getY());
			$this->putByte($record->getZ());
		}
		
		$this->putFloat($this->motionX);
		$this->putFloat($this->motionY);
		$this->putFloat($this->motionZ);
	}
}
