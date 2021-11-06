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
			$blockPos = $block->getPosition();
			/** @var TNT[] $tnt */
			$tnt = [World::blockHash($blockPos->x, $blockPos->y, $blockPos->z) => $block];
			$this->searchForTNT($tnt, $block);
			foreach($tnt as $otherTNT){
				$otherTNT->ignite(80);
			}
			$event->cancel();
		}
	}

	/**
	 * @param TNT[] $tnt
	 */
	private function searchForTNT(array &$tnt, TNT $current) : void{
		foreach($current->getAllSides() as $side){
			$position = $side->getPosition();
			if($side instanceof TNT and !isset($tnt[$hash = World::blockHash($position->x, $position->y, $position->z)])){
				$tnt[$hash] = $side;
				$this->searchForTNT($tnt, $side);
			}
		}
	}
}
