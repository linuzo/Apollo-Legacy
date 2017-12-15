<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\raknet\server;

use pocketmine\network\raknet\Binary;
use pocketmine\network\raknet\RakNet;
use pocketmine\network\raknet\protocol\EncapsulatedPacket;

class ServerHandler{

    /** @var RakNetServer */
    protected $server;
    /** @var ServerInstance */
    protected $instance;

    public function __construct(RakNetServer $server, ServerInstance $instance){
        $this->server = $server;
        $this->instance = $instance;
    }

    public function sendEncapsulated($identifier, EncapsulatedPacket $packet, $flags = RakNet::PRIORITY_NORMAL){
        $buffer = chr(RakNet::PACKET_ENCAPSULATED) . chr(strlen($identifier)) . $identifier . chr($flags) . $packet->toBinary(true);
        $this->server->pushMainToThreadPacket($buffer);
    }
	
	public function sendReadyEncapsulated($buffer){
		$this->server->pushMainToThreadPacket($buffer);
	}

    public function sendRaw($address, $port, $payload){
        $buffer = chr(RakNet::PACKET_RAW) . chr(strlen($address)) . $address . Binary::writeShort($port) . $payload;
        $this->server->pushMainToThreadPacket($buffer);
    }

    public function closeSession($identifier, $reason){
        $buffer = chr(RakNet::PACKET_CLOSE_SESSION) . chr(strlen($identifier)) . $identifier . chr(strlen($reason)) . $reason;
        $this->server->pushMainToThreadPacket($buffer);
    }

    public function sendOption($name, $value){
        $buffer = chr(RakNet::PACKET_SET_OPTION) . chr(strlen($name)) . $name . $value;
        $this->server->pushMainToThreadPacket($buffer);
    }

    public function blockAddress($address, $timeout){
        $buffer = chr(RakNet::PACKET_BLOCK_ADDRESS) . chr(strlen($address)) . $address . Binary::writeInt($timeout);
        $this->server->pushMainToThreadPacket($buffer);
    }

    public function shutdown(){
        $buffer = chr(RakNet::PACKET_SHUTDOWN);
        $this->server->pushMainToThreadPacket($buffer);
        $this->server->shutdown();
        $this->server->synchronized(function(){
			if(!is_null($this->server)){
				$this->server->wait(20000);
			}
        });
        
        $this->server->join();
    }

    public function emergencyShutdown(){
	    $this->server->shutdown();
        $this->server->pushMainToThreadPacket("\x7f"); //RakNet::PACKET_EMERGENCY_SHUTDOWN
    }

    protected function invalidSession($identifier){
        $buffer = chr(RakNet::PACKET_INVALID_SESSION) . chr(strlen($identifier)) . $identifier;
        $this->server->pushMainToThreadPacket($buffer);
    }

    /**
     * @return bool
     */
    public function handlePacket(){
        if(strlen($packet = $this->server->readThreadToMainPacket()) > 0){
            $id = ord($packet{0});
            $offset = 1;
            if($id === RakNet::PACKET_ENCAPSULATED){
                $len = ord($packet{$offset++});
                $identifier = substr($packet, $offset, $len);
                $offset += $len;
                $buffer = substr($packet, $offset);
                $this->instance->handleEncapsulated($identifier, $buffer);
			}elseif($id === RakNet::PACKET_PING){
				$len = ord($packet{$offset++});
				$identifier = substr($packet, $offset, $len);
				$offset += $len;
				$len = ord($packet{$offset++});
				$ping = substr($packet, $offset, $len);
				$this->instance->handlePing($identifier, $ping);
            }elseif($id === RakNet::PACKET_RAW){
                $len = ord($packet{$offset++});
                $address = substr($packet, $offset, $len);
                $offset += $len;
                $port = Binary::readShort(substr($packet, $offset, 2));
                $offset += 2;
                $payload = substr($packet, $offset);
                $this->instance->handleRaw($address, $port, $payload);
            }elseif($id === RakNet::PACKET_SET_OPTION){
                $len = ord($packet{$offset++});
                $name = substr($packet, $offset, $len);
                $offset += $len;
                $value = substr($packet, $offset);
                $this->instance->handleOption($name, $value);
            }elseif($id === RakNet::PACKET_OPEN_SESSION){
                $len = ord($packet{$offset++});
                $identifier = substr($packet, $offset, $len);
                $offset += $len;
                $len = ord($packet{$offset++});
                $address = substr($packet, $offset, $len);
                $offset += $len;
                $port = Binary::readShort(substr($packet, $offset, 2));
                $offset += 2;
                $clientID = Binary::readLong(substr($packet, $offset, 8));
                $this->instance->openSession($identifier, $address, $port, $clientID);
            }elseif($id === RakNet::PACKET_CLOSE_SESSION){
                $len = ord($packet{$offset++});
                $identifier = substr($packet, $offset, $len);
                $offset += $len;
                $len = ord($packet{$offset++});
                $reason = substr($packet, $offset);
                $this->instance->closeSession($identifier, $reason);
            }elseif($id === RakNet::PACKET_INVALID_SESSION){
                $len = ord($packet{$offset++});
                $identifier = substr($packet, $offset, $len);
                $this->instance->closeSession($identifier, "Invalid session");
            }

            return true;
        }

        return false;
    }
}