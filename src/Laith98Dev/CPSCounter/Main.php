<?php

declare(strict_types=1);

namespace Laith98Dev\CPSCounter;

use Laith98Dev\CPSCounter\command\CPSCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase implements Listener
{
	private static ?self $instance = null;

	public static function getInstance() : ?self
	{
		return self::$instance;
	}

	public function onEnable() : void
	{
		self::$instance = $this;
		$this->saveResource("config.yml");
		ConfigHolder::init($this);

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getCommandMap()->register($this->getName(), new CPSCommand($this));
	}

	public function onJoin(PlayerJoinEvent $event) : void
	{
		Session::get($event->getPlayer());
	}

	public function onQuit(PlayerQuitEvent $event) : void
	{
		Session::remove($event->getPlayer());
	}

	/**
	 * @handleCancelled false
	 * @priority HIGHEST
	 */
	public function onDataReceive(DataPacketReceiveEvent $event) : void
	{
		$player = $event->getOrigin()->getPlayer();
		$packet = $event->getPacket();

		if($player === null){
			return;
		}

		$session = Session::get($player);

		if(
			$packet instanceof LevelSoundEventPacket && $packet->sound === LevelSoundEvent::ATTACK_NODAMAGE ||
			$packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData
		){
			$session->update();

			if($session->getCPS() >= ConfigHolder::getLimit()){
				switch (ConfigHolder::getLimitAction()){
					case ConfigHolder::KEY_KICK_ACTION:
						Main::getInstance()->getScheduler()->scheduleDelayedTask(
							new ClosureTask(
								fn() => $player->kick(ConfigHolder::getKickMessage())
							),
							0
						);
						break;
					case ConfigHolder::KEY_CANCEL_ACTION:
						$event->cancel();
						break;
				}
				return;
			}
		}
	}
}
