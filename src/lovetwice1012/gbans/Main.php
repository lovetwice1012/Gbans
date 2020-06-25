<?php

namespace lovetwice1012\gbans;

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
    public function onPreLogin(PlayerPreLoginEvent $event){
        $player = $event->getPlayer();
        $name   = $player->getName();
        if($this->isbanned($name)){
        $event->setkickMessage("§4あなたはBANされています。");
        $event->setCancelled();
        }
    }
    public function isbanned($name){
        $url = 'http://passionalldb.s1008.xrea.com/gban/check.php';

        $data = array(
            'check' => 'check',
            'username' => $name
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = file_get_contents($url, false, stream_context_create($context));
        if($result=="Banned"){
            return true;
        }else{
            return false;
        }
        
    }
         
}
