<?php

namespace darksystem\crossplatform\network\protocol\Play\Server;

use darksystem\crossplatform\network\OutboundPacket;

class StatisticsPacket extends OutboundPacket{

	/** @var int */
	public $count;
	/** @var array */
	public $statistic = [];

	public function pid(){
		return self::STATISTICS_PACKET;
	}

	protected function encode(){
		$this->putVarInt($this->count);
		foreach($this->statistic as $statistic){
			$this->putString($statistic[0]);
			$this->putVarInt($statistic[1]);
		}
	}
}