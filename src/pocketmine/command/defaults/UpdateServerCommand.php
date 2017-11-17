<?php
namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Utils;

class UpdateServerCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.updateserver.description",
			"%pocketmine.command.updateserver.usage",
			["serverupdate"]
		);
		$this->setPermission('pocketmine.command.updateserver');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$apollocfg = yaml_parse_file('apollo.yml');
		$branch = $apollocfg['updater']['branch'];
		$raw = json_decode(Utils::getURL('https://circleci.com/api/v1/project/Apollo-SoftwareTeam/Apollo-Legacy/tree/'.$branch.'?circle-token:token&limit=1&offset=1&filter=successfull'), true);
		$buildinfo = $raw[0];
		if(file_exists('Apollo#'.$buildinfo['build_num'].'.phar')){
			$sender->sendMessage(new TranslationContainer(TF::RED."%pocketmine.command.updateserver.noupdate"));
		}else{
		    foreach(glob("Apollo*.phar") as $file){
			    unlink($file);
		    }
			$rawartifactdata = json_decode(Utils::getURL('https://circleci.com/api/v1/project/Apollo-SoftwareTeam/Apollo-Legacy/'.$buildinfo['build_num'].'/artifacts?circle-token=:token&branch=:branch&filter=:filter', true));
			$artifactdata = get_object_vars($rawartifactdata[0]);
			if($rawartifactdata == 'NULL'){
				$sender->sendMessage(new TranslationContainer(TF::RED."%pocketmine.command.updateserver.invalidbranch"));
			}else{
		        file_put_contents('Apollo#'.$buildinfo['build_num'].'.phar', Utils::getURL($artifactdata['url']));
			    if(file_exists('Apollo#'.$buildinfo['build_num'].'.phar') && filesize('Apollo#'.$buildinfo['build_num'].'.phar') > 0){
			        $sender->sendMessage(new TranslationContainer(TF::GREEN."%pocketmine.command.updateserver.success", [
					    $buildinfo['build_num']
					]));
				    $sender->sendMessage(new TranslationContainer(TF::RED."%pocketmine.command.updateserver.restart"));
				    sleep(2);
			 	    $sender->getServer()->Shutdown();
			    }else{
				    $sender->sendMessage(new TranslationContainer(TF::RED."%pocketmine.command.updateserver.error"));
			    }
			}
	    }
	}
}
