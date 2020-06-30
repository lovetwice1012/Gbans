<?php

namespace lovetwice1012\gbans;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\utils\TextFormat as Color;

class Main extends PluginBase implements Listener
{
    public $data;
    public $plugin;
    public $cver = "1.4.0";
    public $alert = false;
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);        
        $this->load();
        $this->plugin = $this;               
    }    
    public function load()
    {
	if (!(file_exists($this->getDataFolder()))) @mkdir($this->getDataFolder());
        date_default_timezone_set('Asia/Tokyo');
        $this->config = new Config($this->getDataFolder() . "whitelist.yml", Config::YAML);
	    $url = 'http://passionalldb.s1008.xrea.com/gban/ver2.php';

        $data = array(
            'checkver' => 'checkver'
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = @file_get_contents($url, false, stream_context_create($context));
	    $data=json_decode($result);
	    $next = $data[0];
	    $VI = $data[1];
	    if($this->cver!=$next){
		    if($VI){
			 $this->alert=true;   
			    $this->getLogger()->info(Color::RED . "[GBan]とても重要なアップデートがあります。アップデートしないと、GBanの動作に致命的な影響を及ぼす可能性があります。すぐにアップデートをしてください。");
		    }else{
			    $this->getLogger()->info(Color::RED . "[GBan]新しいバージョンがあります。アップデートしてください。");
		    }
	    }
}
    public function onPreLogin(PlayerPreLoginEvent $event){
        $player = $event->getPlayer();
        $name   = $player->getName();
	if(!$this->config->exists($name)){
        	if($this->isbanned($name)){
        	$event->setkickMessage("§4あなたはBANされています。");
        	$event->setCancelled();
        	}
	}
    }
	public function onJoin(PlayerJoinEvent $event){

	    $url = 'http://passionalldb.s1008.xrea.com/gban/ver2.php';

        $data = array(
            'checkver' => 'checkver'
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = @file_get_contents($url, false, stream_context_create($context));
	    $data=json_decode($result);
	    $next = $data[0];
	    $VI = $data[1];
	    if($this->cver!=$next){
		    if($VI){
			 $this->alert=true;   
			    $this->getLogger()->info(Color::RED . "[GBan]とても重要なアップデートがあります。アップデートしないと、GBanの動作に致命的な影響を及ぼす可能性があります。すぐにアップデートをしてください。");
		    }else{
			    $this->getLogger()->info(Color::RED . "[GBan]新しいバージョンがあります。アップデートしてください。");
		    }
	    }
    

	if($this->alert&&$event->getPlayer()->isOp()){
		$event->getPlayer()->sendMessage("§4[GBan]とても重要なアップデートがあります。アップデートしないと、GBanの動作に致命的な影響を及ぼす可能性があります。すぐにアップデートをしてください。");
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

        $result = @file_get_contents($url, false, stream_context_create($context));
        if($result=="Banned"){
            return true;
        }else{
            return false;
        }
      
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool
	{
	    if ($player instanceof Player){
		    $this->getLogger()->info(Color::RED . "コンソールからの操作はサポート外になりました。");
	            return true;
	    }
	    if ($command->getName() === "gban"){
            
            if (empty($args[0])||empty($args[1])){
                $sender->sendMessage(" §b使い方 : /gban <プレイヤーのゲーマータグ> <理由>");
                return true;
            }
            if($this->ban($args[0],$args[1],$sender->getName())){
            $player = Server::getInstance()->getPlayer($args[1]);

            if ($player instanceof Player){
                $player->kick("§4あなたはbanされました。 \n§6理由 §f: §6$args[1] ", false);
            }
                $sender->sendMessage("グローバルbanしました。");
                return true;
            }else{
                $sender->sendMessage("グローバルbanできませんでした。このサーバーからやあなたからのBAN申請がブロックされているか、サーバーがサービスの提供を一時停止している、もしくはサーバー側でエラーが発生した可能性があります。Gbanプラグインを最新版にアップデートすると解決する場合があります。それでも解決しない場合はしばらく時間をおくか、公式discord-bot「GBans-official」を使用してBanを試みてください。");
                return true;
            }
            if ($command->getName() === "gunban"){
            
            if (empty($args[0])||empty($args[1])){
                $sender->sendMessage(" §b使い方 : /gunban <プレイヤーのゲーマータグ>");
                return true;
            }
            if($this->unban($args[0],$args[1],$sender->getName())){
           
                $sender->sendMessage("グローバルunbanしました。");
                return true;
            }else{
                $sender->sendMessage("グローバルunbanできませんでした。このサーバーからやあなたからのUNBAN申請がブロックされているか、サーバーがサービスの提供を一時停止している、もしくはサーバー側でエラーが発生した可能性があります。Gbanプラグインを最新版にアップデートすると解決する場合があります。それでも解決しない場合はしばらく時間をおいて再度試してみてください。");
                $sender->sendMessage("§4[注意]UNBANはBANした人本人がUNBANしていて、BANした時にいたサーバーで行わないと拒否されます。");
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

        $result = @file_get_contents($url, false, stream_context_create($context));
        if($result=="success"){
            return true;
        }else{
            return false;
        }
        
    }      
    public function unban($name,$reason,$user){

        $url = 'http://passionalldb.s1008.xrea.com/gban/unban.php';

        $data = array(
            'unban' => 'unban',
            'username' => $name,
            'user' => $user
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = @file_get_contents($url, false, stream_context_create($context));
        if($result=="success"){
            return true;
        }else{
            return false;
        }
        
    }      

}
