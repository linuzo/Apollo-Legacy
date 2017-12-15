<?php

namespace pocketmine\network\raknet\protocol;

use pocketmine\network\raknet\RakNet;

class UNCONNECTED_PONG extends Packet{
	
    public static $ID = 0x1c;

    public $pingID;
    public $serverID;
    public $serverName;

    public function encode(){
        parent::encode();
        $this->putLong($this->pingID);
        $this->putLong($this->serverID);
        $this->put(RakNet::MAGIC);
        $this->putString($this->serverName);
    }

    public function decode(){
        parent::decode();
        $this->pingID = $this->getLong();
        $this->serverID = $this->getLong();
        $this->offset += 16;
        $this->serverName = $this->getString();
    }
}