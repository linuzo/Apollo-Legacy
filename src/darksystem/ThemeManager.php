<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace darksystem;

use pocketmine\Server;

class ThemeManager{
	
	public $availableThemes = [];
	
	const DEFAULT_THEME = "classic";
	
	public function __construct(Server $server){
		$this->server = $server;
	}
	
	public function getTheme(){
		$configTheme = $this->server->getConfigString("theme", ThemeManager::DEFAULT_THEME);
		
		if($this->server->getConfigInt("colorful-theme", "false")){
		    return $this->availableThemes[array_rand($this->availableThemes)];
		}
		
		if($configTheme === null){
			return false;
		}
		
		return $configTheme;
    }
    
    public function setTheme($value){
    	if(!in_array($value, $this->availableThemes)){
    	    return false;
    	}
    
    	if($value == $this->getTheme()){
    	    return false;
    	}
    
		$this->server->setConfigString("theme", $value);
    }
    
    public function getDefaultTheme(){
    	return ThemeManager::DEFAULT_THEME;
    }
    
    //ASCII Text Font: Doom
    public function getLogoTheme($version, $mcpe, $protocol, $codename, $splash){
    	$name = $this->server->getSoftwareName();
    	if(mt_rand(1, 100) == 1){ //%1 chance
    	    $random = substr(base64_encode(random_bytes(20)), 3, 10);
    	    return 
    
		}
			break;
		}
    }   
}
