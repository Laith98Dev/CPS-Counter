<?php

declare(strict_types=1);

namespace Laith98Dev\CPSCounter;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

final class ConfigHolder
{
	public const KEY_CPS_LIMIT = "cps-limit";
	public const KEY_CPS_LIMIT_ACTION = "cps-limit-action";
	public const KEY_CPS_LIMIT_KICK_MESSAGE = "cps-limit-kick-message";
	public const KEY_CPS_POPUP_MESSAGE = "cps-popup-message";

	public const KEY_KICK_ACTION = "kick";
	public const KEY_CANCEL_ACTION = "cancel";

	private static Config $config;

	public static function init(Main $plugin) : void
	{
		self::$config = $plugin->getConfig();
	}

	public static function getLimit() : int
	{
		return self::$config->get(self::KEY_CPS_LIMIT, 25);
	}

	public static function getLimitAction() : string
	{
		return self::$config->get(self::KEY_CPS_LIMIT_ACTION, self::KEY_KICK_ACTION);
	}

	public static function getKickMessage() : string
	{
		return TextFormat::colorize(self::$config->get(self::KEY_CPS_LIMIT_KICK_MESSAGE, "§cYou were kicked because you exceeded the CPS limit"));
	}

	public static function getPopupMessage() : string
	{
		return TextFormat::colorize(self::$config->get(self::KEY_CPS_POPUP_MESSAGE, "§eYour CPS: §f{cps}"));
	}
}
