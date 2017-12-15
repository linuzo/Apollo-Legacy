<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\utils;

use LogLevel;
use pocketmine\Server;
use darksystem\Thread;
use darksystem\Worker;
use pocketmine\Translate;
use darksystem\ThemeManager;
use pocketmine\utils\TextFormat as TF;

class MainLogger extends \AttachableThreadedLogger{
	
	protected $logFile;
	protected $logStream;
	protected $shutdown;
	protected $logDebug;
	
	public static $logger = null;
	
	public $shouldSendMsg = "";
	public $shouldRecordMsg = false;
	
	private $logResource;
	private $lastGet = 0;
	
	public function setSendMsg($b){
		$this->shouldRecordMsg = $b;
		$this->lastGet = time();
	}

	public function getMessages(){
		$msg = $this->shouldSendMsg;
		$this->shouldSendMsg = "";
		$this->lastGet = time();
		return $msg;
	}
	
	public function __construct($logFile, $logDebug = false){
		if(static::$logger instanceof MainLogger){
			throw new \RuntimeException("Sunucu Konsolu Zaten Oluşturulmuş!");
		}
		static::$logger = $this;
		$this->logStream = new \Threaded;
		$this->start();
	}
	
	public static function getLogger(){
		return static::$logger;
	}

	public function emergency($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::EMERGENCY, "ACIL", TF::RED);
		}else{
			$this->send($message, \LogLevel::EMERGENCY, "EMERGENCY", TF::RED);
		}
	}

	public function alert($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::ALERT, "IKAZ", TF::RED);
		}else{
			$this->send($message, \LogLevel::ALERT, "ALERT", TF::RED);
		}
	}

	public function critical($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::CRITICAL, "KRITIK", TF::RED);
		}else{
			$this->send($message, \LogLevel::CRITICAL, "CRITICAL", TF::RED);
		}
	}

	public function error($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::ERROR, "HATA", TF::RED);
		}else{
			$this->send($message, \LogLevel::ERROR, "ERROR", TF::RED);
		}
	}

	public function warning($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::WARNING, "UYARI", TF::GOLD);
		}else{
			$this->send($message, \LogLevel::WARNING, "WARNING", TF::GOLD);
		}
	}

	public function notice($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::NOTICE, "BILDIRIM", TF::GRAY);
		}else{
			$this->send($message, \LogLevel::NOTICE, "NOTICE", TF::GRAY);
		}
	}

	public function info($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::INFO, "BILGI", TF::YELLOW);
		}else{
			$this->send($message, \LogLevel::INFO, "INFO", TF::YELLOW);
		}
	}

	public function debug($message, $name = "ONARIM"){
		if($this->logDebug === false){
			return false;
		}
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::DEBUG, $name, TF::GRAY);
		}else{
			$this->send($message, \LogLevel::DEBUG, "DEBUG", TF::GRAY);
		}
	}
	
	public function setLogDebug($logDebug){
		$this->logDebug = (bool) $logDebug;
	}

	public function logException(\Throwable $e, $trace = null){
		if($trace === null){
			$trace = $e->getTrace();
		}
		$errstr = $e->getMessage();
		$errfile = $e->getFile();
		$errno = $e->getCode();
		$errline = $e->getLine();
		if(Translate::checkTurkish() === "yes"){
		$errorConversion = [
			0 => "EXCEPTION",
			E_ERROR => "E_HATA",
			E_WARNING => "E_UYARI",
			E_PARSE => "E_OKUMA",
			E_NOTICE => "E_BILDIRIM",
			E_CORE_ERROR => "E_CORE_HATASI",
			E_CORE_WARNING => "E_CORE_UYARISI",
			E_COMPILE_ERROR => "E_COMPILE_HATASI",
			E_COMPILE_WARNING => "E_COMPILE_UYARISI",
			E_USER_ERROR => "E_KULLANICI_HATASI",
			E_USER_WARNING => "E_KULLANICI_UYARISI",
			E_USER_NOTICE => "E_KULLANCI_BILDIRIMI",
			E_STRICT => "E_STRICT",
			E_RECOVERABLE_ERROR => "E_RECOVERABLE_HATA",
			E_DEPRECATED => "E_DEPRECATED",
			E_USER_DEPRECATED => "E_KULLANICI_DEPRECATED",
		];
		}else{
		$errorConversion = [
			0 => "EXCEPTION",
			E_ERROR => "E_ERROR",
			E_WARNING => "E_WARNING",
			E_PARSE => "E_PARSE",
			E_NOTICE => "E_NOTICE",
			E_CORE_ERROR => "E_CORE_ERROR",
			E_CORE_WARNING => "E_CORE_WARNING",
			E_COMPILE_ERROR => "E_COMPILE_ERROR",
			E_COMPILE_WARNING => "E_COMPILE_WARNING",
			E_USER_ERROR => "E_USER_ERROR",
			E_USER_WARNING => "E_USER_WARNING",
			E_USER_NOTICE => "E_USER_NOTICE",
			E_STRICT => "E_STRICT",
			E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
			E_DEPRECATED => "E_DEPRECATED",
			E_USER_DEPRECATED => "E_USER_DEPRECATED",
		];
		}
		if($errno === 0){
			$type = LogLevel::CRITICAL;
		}else{
			$type = ($errno === E_ERROR || $errno === E_USER_ERROR) ? LogLevel::ERROR : (($errno === E_USER_WARNING || $errno === E_WARNING) ? LogLevel::WARNING : LogLevel::NOTICE);
		}
		$errno = isset($errorConversion[$errno]) ? $errorConversion[$errno] : $errno;
		if(($pos = strpos($errstr, "\n")) !== false){
			$errstr = substr($errstr, 0, $pos);
		}
		$errfile = \pocketmine\cleanPath($errfile);
		$this->log($type, get_class($e) . ": \"$errstr\" ($errno) in \"$errfile\" at line $errline");
		foreach(@\pocketmine\getTrace(1, $trace) as $i => $line){
			$this->debug($line);
		}
	}

	public function log($level, $message){
		switch($level){
			case LogLevel::EMERGENCY:
				$this->emergency($message);
				break;
			case LogLevel::ALERT:
				$this->alert($message);
				break;
			case LogLevel::CRITICAL:
				$this->critical($message);
				break;
			case LogLevel::ERROR:
				$this->error($message);
				break;
			case LogLevel::WARNING:
				$this->warning($message);
				break;
			case LogLevel::NOTICE:
				$this->notice($message);
				break;
			case LogLevel::INFO:
				$this->info($message);
				break;
			case LogLevel::DEBUG:
				$this->debug($message);
				break;
		}
	}

	public function shutdown(){
		$this->shutdown = true;
	}

	protected function send($message, $level, $prefix, $color){
		$now = time();
		$thread = \Thread::getCurrentThread();
		if($message == ""){
			return false;
		}
		if($thread === null){
			if(Translate::checkTurkish() === "yes"){
				$threadName = "Sunucu İşlemi";
			}else{
				$threadName = "Server Thread";
			}
		}elseif($thread instanceof Thread || $thread instanceof Worker){
			if(Translate::checkTurkish() === "yes"){
				$threadName = $thread->getThreadName() . " İşlemi";
			}else{
				$threadName = $thread->getThreadName() . " Thread";
			}
		}else{
			if(Translate::checkTurkish() === "yes"){
				$threadName = (new \ReflectionClass($thread))->getShortName() . " İşlemi";
			}else{
				$threadName = (new \ReflectionClass($thread))->getShortName() . " Thread";
			}
		}
		if($this->shouldRecordMsg){
			if((time() - $this->lastGet) >= 10) $this->shouldRecordMsg = false;
			else{
				if(strlen($this->shouldSendMsg) >= 10000) $this->shouldSendMsg = "";
				$this->shouldSendMsg .= $color . "|" . $prefix . "|" . trim($message, "\r\n") . "\n";
			}
		}
		$name = \pocketmine\NAME;
		$easter = "LOL";
		$message = TF::toANSI("§" . mt_rand(1, 9) . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§" . mt_rand(1, 9) . "》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
		//Not works correctly
		/*switch(Server::getInstance()->getTheme()){
			case "darkness":
			//Server::getInstance()->getThemeManager()->setTheme(Server::getInstance()->getThemeManager()->getDefaultTheme());
			$message = TF::toANSI(TF::GREEN . "<" . date("H:i:s", $now) . "> " . TF::AQUA . $easter . " §l§6》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
			case "classic":
			$message = TF::toANSI(TF::AQUA . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§6》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
			case "dark":
			$message = TF::toANSI(TF::GRAY . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§3》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
			case "light":
			$message = TF::toANSI(TF::WHITE . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§f》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
			case "metal":
			$message = TF::toANSI(TF::GRAY . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§f》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
			case "energy":
			$message = TF::toANSI(TF::YELLOW . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§6》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
			case "uranium":
			$message = TF::toANSI(TF::GREEN . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§e》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
			default;
			$message = TF::toANSI(TF::AQUA . "<" . date("H:i:s", $now) . "> " . TF::BLUE . $name . " §l§6》§r " . $color . $prefix . ":" . TF::SPACE . $message . TF::RESET);
			break;
		}*/
		$cleanMessage = TF::clean($message);
		if(!Terminal::hasFormattingCodes()){
			echo $cleanMessage . PHP_EOL;
		}else{
			echo $message . PHP_EOL;
		}
		if($this->attachment instanceof \ThreadedLoggerAttachment){
			$this->attachment->call($level, $message);
		}
		$this->logStream[] = date("Y-m-d", $now) . TF::SPACE . $cleanMessage . "\n";
		if($this->logStream->count() == 1){
			$this->synchronized(function(){
				$this->notify();
			});
		}
		return true;
	}
	
	public function directSend($message){
		$message = TF::toANSI($message);
		$cleanMessage = TF::clean($message);
		if(!Terminal::hasFormattingCodes()){
			echo $cleanMessage . PHP_EOL;
		}else{
			echo $message . PHP_EOL;
		}
		return true;
	}
	
	public static function clear(){
		//echo chr(27) . chr(91) . "H" . chr(27) . chr(91) . "J";
		//echo str_repeat(" \n", 40);
	}
	
	public function run(){
		$this->shutdown = false;
	}
	
}
