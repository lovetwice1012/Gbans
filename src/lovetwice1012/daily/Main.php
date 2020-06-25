<?php

namespace lovetwice1012\daily;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;

class Main extends PluginBase implements Listener
{
    public $data;
    public $plugin;
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);        
        $this->load();
        $this->plugin = $this;               
    }    
    public function load()
    {
        date_default_timezone_set('Asia/Tokyo');
        //まだ準備できてない
        //$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        
    }
         
}
