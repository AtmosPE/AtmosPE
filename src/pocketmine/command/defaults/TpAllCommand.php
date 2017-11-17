<?php

namespace pocketmine\command\defaults;
use pocketmine\event\Listener;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
class TpAllCommand extends VanillaCommand{
	
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.tpall.description",
			"%commands.tpall.usage"
		);
		$this->setPermission("pocketmine.command.tpall");
	}
	
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "That command can't be used from the console!");
			return true;
		}
		
		if(count($args) >= 1){
			$sender->sendMessage(TextFormat::RED . "Error!");
			return true;
		}
		
		$players = count($sender->getServer()->getOnlinePlayers());
        if($players <= 1){
        	$sender->sendMessage(TextFormat::RED . "Sorry, no players are online at the moment!");
        	return true;
        }else{
        	foreach($sender->getServer()->getOnlinePlayers() as $p){
        	    $p->teleport($sender);
			}
		}
		
		$sender->sendMessage(TextFormat::GREEN . "Teleported all players!");
		return true;
	}
