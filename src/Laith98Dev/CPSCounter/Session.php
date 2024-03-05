<?php

declare(strict_types=1);

namespace Laith98Dev\CPSCounter;

use pocketmine\player\Player;
use function microtime;
use function str_replace;

final class Session
{
	/**
	 * WeakMap ensures that the session is destroyed when the player is destroyed, without causing any memory leaks
	 *
	 * @phpstan-var \WeakMap<Player, Session>
	 */
	private static \WeakMap $data;

	public static function get(Player $player) : Session{
		if(!isset(self::$data)){
			/** @phpstan-var \WeakMap<Player, Session> $map */
			$map = new \WeakMap();
			self::$data = $map;
		}

		return self::$data[$player] ??= self::loadSessionData($player);
	}

	public static function remove(Player $player){
		if(isset(self::$data[$player])){
			unset(self::$data[$player]);
		}
	}

	private static function loadSessionData(Player $player) : Session{
		return new Session($player, 0, microtime(true));
	}

	public function __construct(
		private Player $player,
		private int $cps,
		private float $time,
	){
		// NOOP
	}

	public function getCPS() : int
	{
		return $this->cps;
	}

	public function update() : void
	{
		if((microtime(true) - $this->time) > 1){
			$this->cps = 0;
			$this->time = microtime(true);
		}

		$this->cps++;

		if ($this->player->isConnected()){
			$this->player->sendPopup(str_replace(["{player}", "{cps}"], [$this->player->getName(), $this->getCPS()], ConfigHolder::getPopupMessage()));
		}
	}
}
