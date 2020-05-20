<?php

/**
 * Copyright (c) 2020 PJZ9n.
 *
 * This file is part of GiveMoney.
 *
 * GiveMoney is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GiveMoney is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GiveMoney. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace PJZ9n\GiveMoney;

use PJZ9n\MoneyConnector\Connectors\EconomyAPI;
use PJZ9n\MoneyConnector\Connectors\MixCoinSystem;
use PJZ9n\MoneyConnector\Connectors\MoneySystem;
use PJZ9n\MoneyConnector\MoneyConnector;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use RuntimeException;

class Main extends PluginBase implements Listener
{
    /** @var MoneyConnector */
    private $moneyConnector;
    
    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $moneyAPI = $this->getConfig()->get("use-economy-api");
        switch ($moneyAPI) {
            case "EconomyAPI":
                $this->moneyConnector = new EconomyAPI();
                break;
            case "MixCoinSystem":
                $this->moneyConnector = new MixCoinSystem();
                break;
            case "MoneySystem":
                $this->moneyConnector = new MoneySystem();
                break;
            default:
                throw new RuntimeException("API \"{$moneyAPI}\" is not supported");
        }
        $this->getLogger()->info("Using {$moneyAPI}!");
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $amount = (int)$this->getConfig()->get("money-amount");
        $unit = $this->moneyConnector->getMonetaryUnit();
        $player->sendMessage("Hey {$player->getName()}! we have a gift for you! Given {$unit}{$amount} !");
        $this->moneyConnector->addMoney($player, $amount);
    }
}