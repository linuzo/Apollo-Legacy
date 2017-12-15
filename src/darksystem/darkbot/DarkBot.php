<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace darksystem\darkbot;

use pocketmine\Player;
use pocketmine\Server;
use darksystem\Thread;
use pocketmine\Translate;
use pocketmine\level\Level;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\utils\MainLogger;
use pocketmine\utils\Utils;
use pocketmine\utils\UUID;

class DarkBot extends Thread{
	
	private $server;
	
	protected $active = true;
	
	const PREFIX = "§bDARKBOT: §r";
	
	public function __construct(Server $server){
		$this->server = $server;
	}
	
	public function getThreadName(){
		return "DarkBot";
	}
	
	public function getStartupMessage(){
		if(Translate::checkTurkish() === "yes"){
			return DarkBot::PREFIX . "§aSunucuyu Ben Yönetiyorum!";
		}else{
			return DarkBot::PREFIX . "§aI manage server!";
		}
	}
	
	public function check(){
		if($this->active){
			return "✔";
		}else{
			return "❌";
		}
	}
	
	public function run(){
		$enabled = true;
		if($enabled){
			$this->active = true;
		}else{
			$this->active = false;
		}
	}
	
	public function shutdown(){
		$this->active = false;
	}
	
	public function spawn($name, $eid, $x, $y, $z, $skin, $item){
		$this->pk = new AddPlayerPacket();
		$this->pk->uuid = UUID::fromRandom();
		$this->pk->username = $name;
		$this->pk->eid = $eid;
		$this->pk->x = $x;
		$this->pk->y = $y;
		$this->pk->z = $z;
		$this->pk->skin = $skin;			
		$this->pk->speedX = 0;
		$this->pk->speedY = 0;
		$this->pk->speedZ = 0;
		$this->pk->yaw = 0;
		$this->pk->pitch = 0;
		$this->pk->item = $item;
		$this->pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 0],
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 0 << Entity::DATA_FLAG_SILENT],
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 1 << Entity::DATA_FLAG_NO_AI],
			//Entity::DATA_FLAGS => [Entity::DATA_FLAG_SHOW_NAMETAG, true],
			//Entity::DATA_FLAGS => [Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG, true],
			Entity::DATA_LEAD_HOLDER => [Entity::DATA_TYPE_LONG, -1],
			Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1],
		];
		
		foreach($this->server->getOnlinePlayers() as $p){
			$p->dataPacket($this->pk);
			//$p->sendMessage("§bDARKBOT: §aMerhaba!");
		}
		
		//$this->server->broadcastPopup("§aDarkBot Oyuna Katıldı!");
	}
	
}
