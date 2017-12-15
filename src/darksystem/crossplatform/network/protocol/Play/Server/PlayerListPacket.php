<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class PlayerListPacket extends OutboundPacket{

	const TYPE_ADD = 0;
	const TYPE_UPDATE_NAME = 3;
	const TYPE_REMOVE = 4;

	/** @var int */
	public $actionID;
	/** @var array */
	public $players = [];

	public function pid(){
		return self::PLAYER_LIST_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->actionID);
		$this->putVarInt(count($this->players));
		foreach($this->players as $player){
			switch($this->actionID){
				case self::TYPE_ADD:
					$this->put($player[0]);//UUID
					$this->putString($player[1]); //PlayerName
					$this->putVarInt(count($player[2])); //Count Peropetry

					foreach($player[2] as $peropetrydata){
						$this->putString($peropetrydata["name"]); //Name
						$this->putString($peropetrydata["value"]); //Value
						if(isset($peropetrydata["signature"])){
							$this->putBool(true); //Is Signed
							$this->putString($peropetrydata["signature"]); //Peropetry
						}else{
							$this->putBool(false); //Is Signed
						}
					}

					$this->putVarInt($player[3]); //Gamemode
					$this->putVarInt($player[4]); //Ping
					$this->putBool($player[5]); //has Display name
					if($player[5] === true){
						$this->putString($player[6]); //Display name
					}
					break;
				case self::TYPE_UPDATE_NAME:
					$this->put($player[0]);//UUID
					$this->putBool($player[1]); //has Display name
					$this->putString($player[2]);//Display name
					break;
				case self::TYPE_REMOVE:
					$this->put($player[0]);//UUID
					break;
				default:
					echo "PlayerListPacket: ".$this->actionID."\n";
					break;
			}
		}
	}
}
