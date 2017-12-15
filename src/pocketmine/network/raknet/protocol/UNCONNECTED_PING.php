<?php

namespace pocketmine\network\raknet\protocol;

use pocketmine\network\raknet\RakNet;

class UNCONNECTED_PING extends Packet{
	
    public static $ID = 0x01;
    
    public $pingID;
    
    //public $GUID;
    
    public function encode(){
        parent::encode();
        $this->putLong($this->pingID);
        $this->put(RakNet::MAGIC);
        //$this->putLong($this->GUID);
    }

    public function decode(){
        parent::decode();
        $this->pingID = $this->getLong();
        //$this->offset += 16;
        //$this->GUID = $this->getLong();
    }
}