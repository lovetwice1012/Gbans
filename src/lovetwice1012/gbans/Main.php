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
            if($this->ban($args[0],$args[1],$sender->getName())){
            $player = Server::getInstance()->getPlayer($args[0]);

            if ($player instanceof Player){
                $player->kick("§4あなたはbanされました。 \n§6理由 §f: §6$args[1] ", false);
            }
                $sender->sendMessage("グローバルbanしました。");
                return true;
            }else{
                $sender->sendMessage("グローバルbanできませんでした。このサーバーからやあなたからのBAN申請がブロックされているか、サーバーがサービスの提供を一時停止している、もしくはサーバー側でエラーが発生した可能性があります。Gbanプラグインを最新版にアップデートすると解決する場合があります。それでも解決しない場合はしばらく時間をおくか、公式discord-bot「GBans-official」を使用してBanを試みてください。");
                return true;
            }
        }
    }
    
    public function ban($name,$reason,$user){
        $url = 'http://passionalldb.s1008.xrea.com/gban/ban2.php';

        $data = array(
            'ban' => 'ban',
            'username' => $name,
            'reason' => $reason,
	    'user' => $user
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
