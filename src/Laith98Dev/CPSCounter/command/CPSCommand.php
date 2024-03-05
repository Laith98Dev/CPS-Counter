<?php

declare(strict_types=1);

namespace Laith98Dev\CPSCounter\command;

use Laith98Dev\CPSCounter\Main;
use Laith98Dev\CPSCounter\Session;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use function strval;

class CPSCommand extends Command implements PluginOwned
{
	public function __construct(
		private Main $plugin
	){
		parent::__construct("cps", "Get specific player CPS");
		$this->setPermission("cps.command");
	}

	public function getOwningPlugin() : Main
	{
		return $this->plugin;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if(!isset($args[0])){
			$sender->sendMessage(TextFormat::RED . "Usage: /{$commandLabel} <player name>");
			return;
		}

		$player_name = strval($args[0]);

		if(($player = $this->plugin->getServer()->getPlayerByPrefix($player_name)) === null){
			$sender->sendMessage(TextFormat::RED . "No players were found with that name.");
			return;
		}

		$sender->sendMessage(TextFormat::YELLOW . "The CPS for '{$player->getName()}' is " . Session::get($player)->getCPS() . ".");
	}
}
