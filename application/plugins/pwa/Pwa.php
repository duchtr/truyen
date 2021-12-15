<?php
class Pwa extends IPlugin{
	public $hasAdmin =true;
	protected $config;
	protected $resetConfig = false;
	protected $linkOff = 'offline';
	public function __construct(){
		parent::__construct();
		$this->config= $this->getConfigPlugins();
		$this->resetConfig = array_key_exists('reset_config', $this->config)?$this->config['reset_config']:false;
	}
	public function install(){
		$this->createConfig();
		$this->publishAssests();
		$this->addRoutes("Vindex/offline",$this->linkOff);
	}
	public function uninstall(){
		if(ENVIRONMENT != "development"){
			$this->removeConfig();
			$this->removeFile();
		}
		$this->removeRoutes($this->linkOff);
	}
	private function createConfig(){
		if(ENVIRONMENT == "development") return;
		$sql = 'INSERT INTO nuy_detail_region(`id`,`name`, `name_en`) VALUES (9981,"Favicon & PWA","Favicon & PWA")';
		$this->CI->db->query($sql);
		$sqls = [
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON72", "", "", 1, "IMGV2", 9981, "Favicon 72px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON96", "", "", 1, "IMGV2", 9981, "Favicon 96px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON128", "", "", 1, "IMGV2", 9981, "Favicon 128px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON144", "", "", 1, "IMGV2", 9981, "Favicon 144px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON152", "", "", 1, "IMGV2", 9981, "Favicon 152px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON192", "", "", 1, "IMGV2", 9981, "Favicon 192px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON384", "", "", 1, "IMGV2", 9981, "Favicon 384px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "FAVICON512", "", "", 1, "IMGV2", 9981, "Favicon 512px", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "BACKGROUND_COLOR", "", "", 1, "COLOR", 9981, "Background Color", 1, 1);',
			'INSERT INTO `configs`(`keyword`, `vi_value`, `en_value`, `act`, `type`, `region`, `note`, `is_delete`, `ord`) VALUES ( "MAP_LINK", "", "", 1, "TEXT", 9981, "Link Google Map", 1, 1);',
			'INSERT INTO `languages`(`keyword`, `vi_value`, `en_value`, `note`) VALUES ( "OFFLINE_MESSAGE", "Thiết bị của bạn đang không có kết nối internet!", "You need turn on 3G or wifi to access website!", NULL);',
			'INSERT INTO `languages`(`keyword`, `vi_value`, `en_value`, `note`) VALUES ( "MAP_TITLE", "Bản đồ chỉ đường", "Our Address", NULL);'
		];
		foreach ($sqls as $k => $sql) {
			$this->CI->db->query($sql);
		}
	}
	private function publishAssests(){
		$this->publishFile("theme/js/app.js");
		$this->publishFile("theme/js/install.js");
		$file = 'theme/js/sw.js';
		$dir = $this->dir;
		$orgfile = $dir."/".$file;
		if(file_exists($orgfile)){
			$dest = 'pwasw.js';
			copy($orgfile, $dest);
		}
	}
	private function removeConfig(){
		$sql = 'DELETE FROM nuy_detail_region where id = 9981';
		$this->CI->db->query($sql);
		$sql = 'DELETE FROM nuy_config where parent = 9981';
		$this->CI->db->query($sql);
	}
	private function setup(){
		$this->writeManifest();
		$this->removeFile();
		if($this->resetConfig){
			$this->removeConfig();
			$this->createConfig();			
		}
		$this->publishAssests();
		// $this->createOfflineFile();
	}
	private function createOfflineFile(){
		$content = file_get_contents(base_url());
		file_put_contents('offline.html', $content);
	}
	private function writeManifest(){
		$obj = new stdClass;
		$title = $this->CI->Dindex->getSettings('TITLE_SEO');
		$obj->name = $title;
		$obj->short_name = $title;
		$obj->icons = [];
		$sizes = [72,96,128,144,152,192,384,512];
		foreach ($sizes as $k => $size) {
			$tmp = [];
			$tmp['src'] = tech5sGetFavicion($size,true);
			$tmp['sizes'] = $size.'x'.$size;
			$tmp['type'] = 'image/png';
			array_push($obj->icons, $tmp);
		}
		// $obj->start_url = base_url('offline.html');
		$obj->start_url = '/';
		$obj->display = "standalone";
		
		
		$themeColor = $this->CI->Dindex->getSettings('THEME_COLOR');
		$themeColor = isNull($themeColor)?'#ddd':$themeColor;
		$obj->theme_color = $themeColor;
		$themeColor = $this->CI->Dindex->getSettings('BACKGROUND_COLOR');
		$themeColor = isNull($themeColor)?'#ccc':$themeColor;
		$obj->background_color = $themeColor;
		file_put_contents('manifest.json', json_encode($obj));
	}
	public function injectMenuAdmin($args){
		$arrSession = $args["arrSession"];
		$menu = array_key_exists("menu", $arrSession)?$arrSession["menu"]:[];
		$idx = count($menu);
		foreach ($menu as $k => $mitem) {
			if($mitem->name=="Plugins"){
				$idx = $k;
			}
		}
		$link = 'Techsystem/extra?action='.base64_encode("table=pro&action=view&code=pwa");
		if($idx==count($menu)){
			$pluginMenu = new stdClass;
			$pluginMenu->name = "Plugins";
			$pluginMenu->name_en = "Plugins";
			$pluginMenu->icon = "icon-asterisk";
			$pluginMenu->childs = [
				["name"=>"PWA","note"=>"PWA","note_en"=>"PWA","is_server"=>0,"link"=>$link]
			];
		}
		else{
			$pluginMenu = $menu[$idx];
			array_push($pluginMenu->childs, ["name"=>"PWA","note"=>"PWA","note_en"=>"PWA","is_server"=>0,"link"=>$link]);
		}
		$menu[$idx] = $pluginMenu;
		$arrSession["menu"] = $menu;
		return ["arrSession"=>$arrSession];
	}
	public function managerPwa($args){
		$table = $args["table"];
		$action = $args["act"];
		$code = $args["code"];
		if($code==="pwa"){
			$post= $this->CI->input->post();
			if(count($post)>0){
				$config = isset($post["config"])?$post["config"]:"";
				$this->CI->Admindao->updateData(["config"=>$config],'sys_plugins',
						array(
							array('key'=>'name','compare'=>'=','value'=>"'pwa'")
						)
					);
				$this->setup();
			}
			$config = [];
			$plugins = $this->CI->Admindao->getDataInTable("",'sys_plugins',array(
				array('key'=>'name','compare'=>'=',"value"=>"'pwa'")
			),"","");
			if(count($plugins)>0){
				$config = json_decode($plugins[0]["config"],true);
			}
			$link = 'Techsystem/extra?action='.base64_encode("table=pro&action=view&code=pwa");
			$data['content'] = 'pwa.manager';
			$data['link'] = $link;
			$data['config'] = $config;
			$this->CI->load->view('template',$data);
		}
		return true;
	}
	public function insertScript(){
		return '<script defer type="text/javascript" src="'.$this->urlFile("theme/js/app.js").'"></script>';
	}
	public function initVindex(){
		$vindex = &get_instance();
		$page = $this;
		$vindex::macro("offline", function($itemRoutes) use($page){
			$view = "pages.offline";
			if($this->blade->view()->exists($view)){
				echo $this->blade->view()->make($view)->render(null,false);
				
            }
            else{
            	echo $this->blade->view()->make('pwa::offline')->render(null,false);
            }
		});
	}
}