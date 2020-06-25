<?php

namespace lovetwice1012\gbans;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
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
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool
	{
	    if ($command->getName() === "gban"){
            
            if (empty($args[0])){
                $sender->sendMessage(" §b使い方 : /gban <プレイヤーのゲーマータグ> <理由>");
                return true;
            }
            if($this->ban($args[0],$srgs[1])){
                $sender->sendMessage("グローバルbanしました。");
                return true;
            }else{
                $sender->sendMessage("グローバルbanできませんでした。サーバーがサービスの提供を一時停止しているか、サーバー側でエラーが発生した可能性があります。");
                return true;
            }
        }
    }
    
    public function ban($name,$reason){
        $url = 'http://passionalldb.s1008.xrea.com/gban/ban.php';

        $data = array(
            'ban' => 'ban',
            'username' => $name,
            'reason' => $reason
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = file_get_contents($url, false, stream_context_create($context));
        if($result=="success"){
            return true;
        }else{
            return false;
        }
        
    }
         
}
