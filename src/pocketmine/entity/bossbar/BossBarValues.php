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

use pocketmine\entity\Attribute;

class BossBarValues extends Attribute{
	
	public $min, $max, $value, $name;

	public function __construct($min, $max, $value, $name){
		$this->min = $min;
		$this->max = $max;
		$this->value = $value;
		$this->name = $name;
	}

	public function getMinValue(){
		return $this->min;
	}

	public function getMaxValue(){
		return $this->max;
	}

	public function getValue(){
		return $this->value;
	}

	public function getName(){
		return $this->name;
	}

	public function getDefaultValue(){
		return $this->min;
	}
}
