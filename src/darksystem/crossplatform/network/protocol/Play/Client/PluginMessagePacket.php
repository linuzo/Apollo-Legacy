<?php

namespace darksystem\crossplatform\network\protocol\Play\Client;

use darksystem\crossplatform\network\InboundPacket;

class PluginMessagePacket extends InboundPacket{

	/** @var string */
	public $channel;
	/** @var string[] */
	public $data = [];

	public function pid(){
		return self::PLUGIN_MESSAGE_PACKET;
	}

	protected function decode(){
		$this->channel = $this->getString();
		switch($this->channel){
			case "REGISTER":
				$channels = bin2hex($this->getString());
				$channels = str_split($channels, 2);
				$string = "";
				foreach($channels as $num => $str){
					if($str === "00"){
						$this->data[] = hex2bin($string);
						$string = "";
					}else{
						$string .= $str;
						if(count($channels) -1 === $num){
							$this->data[] = hex2bin($string);
						}
					}
				}
			break;
			case "MC|Brand":
				$this->data[] = $this->getString();
			break;
			case "MC|BEdit":
			case "MC|BSign":
				$this->data[] = $this->getSlot();
			break;
		}
	}
}
