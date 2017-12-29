<?php

namespace pocketmine;
	
use pocketmine\Server;

abstract class Thread extends \Thread{
	
	protected $classLoader;
	
	protected $isKilled = false;
	public function getClassLoader(){
		return $this->classLoader;
	}
	public function setClassLoader(\ClassLoader $loader = null){
		if($loader === null){
			$loader = Server::getInstance()->getLoader();
		}
		
		$this->classLoader = $loader;
	}
	public function registerClassLoader(){
		if(!interface_exists("ClassLoader", false)){
			require(\pocketmine\PATH . "src/spl/ClassLoader.php");
			require(\pocketmine\PATH . "src/spl/BaseClassLoader.php");
			//require(\pocketmine\PATH . "src/darksystem/CompatibleClassLoader.php");
		}
		
		if($this->classLoader !== null){
			$this->classLoader->register(true);
		}
	}
	public function start(int $options = \PTHREADS_INHERIT_ALL){
		ThreadManager::getInstance()->add($this);
		if(!$this->isRunning() && !$this->isJoined() && !$this->isTerminated()){
			if($this->getClassLoader() === null){
				$this->setClassLoader();
			}
			
			return parent::start($options);
		}
		return false;
	}
	
	public function quit(){
		$this->isKilled = true;
		
		$this->notify();
		
		if(!$this->isJoined()){
			if(!$this->isTerminated()){
				$this->join();
			}
		}
		
		ThreadManager::getInstance()->remove($this);
	}
	public function getThreadName(){
		return (new \ReflectionClass($this))->getShortName();
	}
	
}
