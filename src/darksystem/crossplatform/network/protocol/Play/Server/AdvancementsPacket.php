<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class AdvancementsPacket extends OutboundPacket{

	/** @var bool */
	public $doClear = false;
	/** @var array */
	public $advancements = [];
	/** @var array */
	public $identifiers = [];
	/** @var array */
	public $progress = [];

	public function pid(){
		return self::ADVANCEMENTS_PACKET;
	}

	protected function encode(){
		$this->putBool($this->doClear);
		$this->putVarInt(count($this->advancements));
		foreach($this->advancements as $advancement){
			$this->putString($advancement[0]);//id
			$this->putBool($advancement[1][0]);//has parent
			if($advancement[1][0]){
				$this->putString($advancement[1][1]);//parent id
			}
			$this->putBool($advancement[2][0]);//has display
			if($advancement[2][0]){
				$this->putString($advancement[2][1]);//title
				$this->putString($advancement[2][2]);//description
				$this->putSlot($advancement[2][3]);//icon (item)
				$this->putVarInt($advancement[2][4]);// frame type
				$this->putInt($advancement[2][5][0]);// flag
				if(($advancement[2][5][0] & 0x01) > 0){
					$this->putString($advancement[2][5][1]);
				}
				$this->putFloat($advancement[2][6]);// x coord
				$this->putFloat($advancement[2][7]);// z coord
			}
			$this->putVarInt(count($advancement[3]));//criteria
			foreach($advancement[3] as $criteria){
				$this->putString($criteria[0]);//key
				//value but void
			}
			$this->putVarInt(count($advancement[4]));
			foreach($advancement[4] as $requirements){//Requirements
				$this->putVarInt(count($requirements));
				foreach($requirements as $requirement){
					$this->putString($requirement);
				}
			}
		}
		$this->putVarInt(count($this->identifiers));
		foreach($this->identifiers as $identifier){
			$this->putString($identifier);
		}
		$this->putVarInt(count($this->progress));
		foreach($this->progress as $progressdata){
			$this->putString($progressdata[0]);//id
			$this->putVarInt(count($progressdata[1]));//Criteria size
			foreach($progressdata[1] as $criterion){
				$this->putString($criterion[0]);//criyeria id
				$this->putBool($criterion[1][0]);//
				if($criterion[1][0]){
					$this->putLong($criterion[1][1]);//time
				}
			}
		}
	}
}
