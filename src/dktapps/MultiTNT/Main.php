<?php

declare(strict_types=1);

namespace dktapps\MultiTNT;

use pocketmine\block\TNT;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\FlintSteel;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class Main extends PluginBase implements Listener{

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onInteract(PlayerInteractEvent $event) : void{
		$block = $event->getBlock();
		$item = $event->getItem();
		if($block instanceof TNT and $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK and $item instanceof FlintSteel){
			/** @var TNT[] $tnt */
			$tnt = [World::blockHash($block->x, $block->y, $block->z) => $block];
			$this->searchForTNT($tnt, $block);
			foreach($tnt as $block){
				$block->ignite(80);
			}
			$event->setCancelled();
		}
	}

	private function searchForTNT(array &$tnt, TNT $current) : void{
		foreach($current->getAllSides() as $side){
			if($side instanceof TNT and !isset($tnt[$hash = World::blockHash($side->x, $side->y, $side->z)])){
				$tnt[$hash] = $side;
				$this->searchForTNT($tnt, $side);
			}
		}
	}
}
