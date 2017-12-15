<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\entity\bossbar;

use pocketmine\entity\Entity;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\BossEventPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\SetEntityDataPacket;
use pocketmine\network\protocol\UpdateAttributesPacket;
use pocketmine\Player;
use pocketmine\Server;

class BossBar{
	
	const ENTITY_NETWORK_ID = 37;

	public static function addBossBar($players, $title, $ticks = null){
		if(empty($players)) return null;

		$eid = Entity::$entityCount++;

		$packet = new AddEntityPacket();
		$packet->eid = $eid;
		$packet->type = self::ENTITY_NETWORK_ID;
		$packet->metadata = [Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 0 ^ 1 << Entity::DATA_FLAG_SILENT ^ 1 << Entity::DATA_FLAG_INVISIBLE ^ 1 << Entity::DATA_FLAG_NO_AI],
			Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title],
			Entity::DATA_BOUNDING_BOX_WIDTH => [Entity::DATA_TYPE_FLOAT, 0],
			Entity::DATA_BOUNDING_BOX_HEIGHT => [Entity::DATA_TYPE_FLOAT, 0]];
		foreach($players as $p){
			$pk = clone $packet;
			$pk->position = $p->getPosition()->asVector3()->subtract(0, 28);
			$p->dataPacket($pk);
		}

		$bpk = new BossEventPacket();
		$bpk->bossEid = $eid;
		$bpk->eventType = BossEventPacket::TYPE_SHOW;
		$bpk->title = $title;
		$bpk->healthPercent = 1;
		$bpk->unknownShort = 0;
		$bpk->color = 0;
		$bpk->overlay = 0;
		$bpk->playerEid = 0;
		Server::getInstance()->broadcastPacket($players, $bpk);

		return $eid;
	}

	public static function sendBossBarToPlayer(Player $player, $eid, $title, $ticks = null){
		self::removeBossBar([$player], $eid);

		$packet = new AddEntityPacket();
		$packet->eid = $eid;
		$packet->type = self::ENTITY_NETWORK_ID;
		$packet->position = $player->getPosition()->asVector3()->subtract(0, 28);
		$packet->metadata = [Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 0 ^ 1 << Entity::DATA_FLAG_SILENT ^ 1 << Entity::DATA_FLAG_INVISIBLE ^ 1 << Entity::DATA_FLAG_NO_AI],
			Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title],
			Entity::DATA_BOUNDING_BOX_WIDTH => [Entity::DATA_TYPE_FLOAT, 0],
			Entity::DATA_BOUNDING_BOX_HEIGHT => [Entity::DATA_TYPE_FLOAT, 0]];
		$player->dataPacket($packet);

		$bpk = new BossEventPacket();
		$bpk->bossEid = $eid;
		$bpk->eventType = BossEventPacket::TYPE_SHOW;
		$bpk->title = $title;
		$bpk->healthPercent = 1;
		$bpk->unknownShort = 0;
		$bpk->color = 0;
		$bpk->overlay = 0;
		$bpk->playerEid = 0;
		$player->dataPacket($bpk);
	}

	public static function setPercentage($percentage, $eid, $players = []){
		if(empty($players)) $players = Server::getInstance()->getOnlinePlayers();
		if(!count($players) > 0) return;
		$upk = new UpdateAttributesPacket();
		$upk->entries[] = new BossBarValues(1, 600, max(1, min([$percentage, 100])) / 100 * 600, 'minecraft:health'); // Ensures that the number is between 1 and 100; //Blame mojang, Ender Dragon seems to die on health 1
		$upk->eid = $eid;
		Server::getInstance()->broadcastPacket($players, $upk);
		$bpk = new BossEventPacket();
		$bpk->bossEid = $eid;
		$bpk->eventType = BossEventPacket::TYPE_SHOW;
		$bpk->title = "";
		$bpk->healthPercent = $percentage / 100;
		$bpk->unknownShort = 0;
		$bpk->color = 0;
		$bpk->overlay = 0;
		$bpk->playerEid = 0;
		Server::getInstance()->broadcastPacket($players, $bpk);
	}

	public static function setTitle($title, $eid, $players = []){
		if(!count(Server::getInstance()->getOnlinePlayers()) > 0) return;
		$npk = new SetEntityDataPacket();
		$npk->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title]];
		$npk->eid = $eid;
		Server::getInstance()->broadcastPacket($players, $npk);
		$bpk = new BossEventPacket();
		$bpk->bossEid = $eid;
		$bpk->eventType = BossEventPacket::TYPE_SHOW;
		$bpk->title = $title;
		$bpk->healthPercent = 1;
		$bpk->unknownShort = 0;
		$bpk->color = 0;
		$bpk->overlay = 0;
		$bpk->playerEid = 0;
		Server::getInstance()->broadcastPacket($players, $bpk);
	}

	public static function removeBossBar($players, $eid){
		if(empty($players)) return false;
		$pk = new RemoveEntityPacket();
		$pk->eid = $eid;
		Server::getInstance()->broadcastPacket($players, $pk);
		return true;
	}
}
