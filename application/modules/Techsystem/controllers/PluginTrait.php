<?php
trait PluginTrait{
	protected $plugins = [];
	protected $checkFolder = false;
	private function _loadPluginsFromDatabase(){
		$this->db->order_by('ord','asc');
		$q = $this->db->get('sys_plugins');
		$plugs = $q->result_array();
		foreach ($plugs as $k => $plug) {
			$dir = PLUGIN_PATH.'/'.$plug['name'];
			$file = $dir.'/config.json';
			if(file_exists($file)){
				$content = json_decode(file_get_contents($file),true);
				$content['dir']=basename ($dir);
				$class =  $this->_getPluginName($content['dir']);
				if(!class_exists($class)) {
					$classFile = PLUGIN_PATH.'/'.$content['dir'].'/'.$class.'.php';
					if(!file_exists($classFile)){
						continue;
					}
					include_once($classFile);
				}
				$insclass = new $class;
				$insclass->data = $plug;
				$this->plugins[$class] = $insclass;
			}
		}
		if(!$this->checkFolder){
			$this->checkFolder = true;
			$this->_loadPluginsFromFolders();
			$this->_loadPluginsFromDatabase();
		}
	}
	
	private function _loadPluginsFromFolders(){
		$directories = glob(PLUGIN_PATH. '/*' , GLOB_ONLYDIR);
		foreach ($directories as $k => $directory) {
			$file = $directory.'/config.json';
			if(file_exists($file)){
				$content = json_decode(file_get_contents($file),true);
				$content['dir']=basename ($directory);
				$class =  $this->_getPluginName($content['dir']);
				if(!class_exists($class)) {
					$classFile = PLUGIN_PATH.'/'.$content['dir'].'/'.$class.'.php';
					if(!file_exists($classFile)){
						// echo 'File $classFile không tồn tại<br>';
						continue;
					}
				}
				if(!array_key_exists($class, $this->plugins)){
					$data = [];
					$data['name'] = $content['dir'];
					$data['act'] = 0;
					$data['ord'] = time();
					$data['create_time'] = time();
					$data['update_time'] = time();
					$data['version'] = isset($content['version'])?$content['version']:0;
					$data['title'] = isset($content['title'])?$content['title']:$data['name'];
					$data['description'] = isset( $content['description'])? $content['description']:'';
					$data['author'] = isset( $content['author'])? $content['author']:'';
					$this->db->insert('sys_plugins',$data);
				}
			}
		}
	}
	private function _getPluginName($str){
		$class =  str_replace('_', '', ucwords($str, '_'));
		return $class;
	}
	private function activePlugin($name){
		$file = PLUGIN_PATH.'/'.$name.'/config.json';
		$class = $this-> _getPluginName($name);
		if(!array_key_exists($class, $this->plugins)){
			return;
		}
		$insclass = $this->plugins[$class];
		$act = $insclass->data['act'];
		$resultHook = $this->hooks->call_hook(['tech5s_plugin_before_change_status','class'=>$insclass,'file'=>$file]);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		if($act==1){
			$content['act']=0;
			$insclass->uninstall();
			$this->db->update('sys_plugins',$content,['id'=>$insclass->data['id']]);
		}
		else{
			$content['act']=1;
			$insclass->install();
			$this->db->update('sys_plugins',$content,['id'=>$insclass->data['id']]);
		}
	}
	function plugins(){
		$this->testLoginAdmin();
		if(isAdminServer() || ENVIRONMENT =='development'){
			$segment = $this->uri->segment(3,'');
			$this->_loadPluginsFromDatabase();
			if($segment==''){
				$resultHook = $this->hooks->call_hook(['tech5s_after_load_plugin','plugins'=>$this->plugins]);
				if(!is_bool($resultHook) && is_array($resultHook)){
					extract($resultHook);
				}
				$data['content']='nuy/plugin';
				$data['plugins']=$this->plugins;
				$this->load->view('template',$data);
			}
			else{
				$this->activePlugin($segment);
				redirect('Techsystem/plugins');
			}
		}
		else{
			redirect('Techsystem');
		}
	}
	private function _deletePlugins($dir){
		$di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
		$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ( $ri as $file ) {
		    $file->isDir() ?  rmdir($file) : unlink($file);
		}
		return true;
	}
	public function deletePlugins(){
		$segment = $this->uri->segment(3,'');
		if($segment!=''){
			$this->db->where('name',$segment);
			$ps = $this->db->get('sys_plugins',1,0)->result_array();
			if(count($ps)>0){
				$p = $ps[0];
				if($p['act']==0){
					$this->db->where('id',$p['id']);
					$this->db->delete('sys_plugins');
					$this->_deletePlugins(PLUGIN_PATH.'/'.$segment);
					unlink(PLUGIN_PATH.'/'.$segment);
				}
			}	
		}
		redirect('Techsystem/plugins');
	}
	private function _searchServerPlugin($post){
		$keyword = isset($post['keyword'])?$post['keyword']:'';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://tech5s.com.vn/demo/analytics/Plugin/search');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close ($ch);
		$plugins = json_decode($result,true);
		$plugins = isset($plugins['data'])?$plugins['data']:[];
		return $plugins;
	}
	private function _searchPlugins(){
		$resultHook = $this->hooks->call_hook(['tech5s_after_install_plugin']);
		if(!is_bool($resultHook) && is_array($resultHook)){
			extract($resultHook);
		}
		if ($this->input->server('REQUEST_METHOD') == 'GET')
		{
			$plugins = [];
		}
		else{
			$post = $this->input->post();
			$plugins = $this->_searchServerPlugin($post);
		}
		$finalPlugins = [];
		foreach ($plugins as $k => $plugin) {
			$plugin['need_update'] = 0;
			$plugin['no_local'] = 0;
			$class =  $this->_getPluginName($plugin['name']);
			if(!array_key_exists($class, $this->plugins)){
				$plugin['no_local'] = 1;
			}
			else{
				$plugin['no_local'] = 0;
				if($this->plugins[$class]->data['version'] != $plugin['version']){
					$plugin['need_update'] = 1;
				}
			}
			array_push($finalPlugins, $plugin);
		}
		return $finalPlugins;
	}
	private function _downloadPlugin($segment,$folder,$username,$password){
		$file = $folder.'/tmp.zip';
		$fp = fopen ($file, 'w+');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://tech5s.com.vn/demo/analytics/Plugin/download/'.$segment);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(['username'=>$username,'password'=>$password]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		$result = curl_exec($ch);
		curl_close ($ch);
		if(file_exists($file)){
			$zip = new ZipArchive;
			$res = $zip->open($file);
			if ($res === TRUE) {
			  $zip->extractTo($folder);
			  $zip->close();
			}
			unlink($file);
		}
	}
	private function _installPlugin($segment){
		if ($this->input->server('REQUEST_METHOD') == 'GET')
		{
		}
		else{
			$post = $this->input->post();
			$username = isset($post['username'])?$post['username']:'';
			$password = isset($post['password'])?$post['password']:'';
			if(strlen($username)>0 && strlen($password)>0){
				$isUpdate = false;
				$folder = PLUGIN_PATH.'/'.$segment;
				if(!file_exists($folder)){
					mkdir($folder,0777,true);
				}
				else{
					$isUpdate = true;
					$this->_deletePlugins($folder);
				}
				$this->_downloadPlugin($segment,$folder,$username,$password);
				$class =  $this->_getPluginName($segment);
				if(!class_exists($class) && $isUpdate) {
					$classFile = $folder.'/'.$class.'.php';
					if(file_exists($classFile)){
						include_once($classFile);
						$insclass = new $class;
						if(method_exists($insclass, 'update')){
							$insclass->update();
						}
					}
				}
				if($isUpdate){
					$this->db->where('name',$segment);
					$this->db->delete('sys_plugins');
				}
				redirect('Techsystem/plugins');
			}
			else{
				redirect('Techsystem/plugins');
			}
		}
	}
	function install(){
		$this->testLoginAdmin();
		if(isAdminServer() || ENVIRONMENT =='development'){
			$this->_loadPluginsFromDatabase();
			$segment = $this->uri->segment(3,'');
			if($segment==''){
				$post = $this->input->post();
				$keyword = isset($post['keyword'])?$post['keyword']:'';
				$finalPlugins = $this->_searchPlugins();
				$data['content']='nuy/install_plugin';
				$data['keyword']=$keyword;
				$data['plugins']=$finalPlugins;
				$this->load->view('template',$data);
			}
			else{
				$this->_installPlugin($segment);
				$finalPlugins = [];
				$data['content']='nuy/install_plugin';
				$data['popup']=1;
				$data['plugins']=$finalPlugins;
				$this->load->view('template',$data);
			}
		}
		else{
			redirect('Techsystem');
		}
	}
	
	
	public function view_plugins(){
		$this->testLoginAdmin();
		if(isAdminServer() || ENVIRONMENT =='development'){
			$segment = $this->uri->segment(3,'');
			if($segment!=''){
				if ($this->input->server('REQUEST_METHOD') == 'GET')
				{
					$file = PLUGIN_PATH.'/'.$segment.'/config.json';
					$class =  $this->_getPluginName($segment);
					if(!class_exists($class)) {
						$classFile = PLUGIN_PATH.'/'.$segment.'/'.$class.'.php';
						if(!file_exists($classFile)){
							die('Class cần khai báo đúng tên: '.$classFile);
						}
						include_once($classFile);
						if(!class_exists($class)) {
							die('Plugin không tồn tại, vui lòng thử lại!');
						}
					}
					$class = new $class;
					if(!$class instanceof IPlugin){
						die('Plugin không đúng định dạng, vui lòng thử lại!');
					}
					$config = [];
					$plugins = $this->Admindao->getDataInTable('','sys_plugins',array(
						array('key'=>'name','compare'=>'=','value'=>addslashes($segment))
					),'','');
					if(count($plugins)>0){
						$config = json_decode($plugins[0]['config'],true);
					}
				}
				else{
					$post = $this->input->post();
					$config = isset($post['config'])?$post['config']:'[]';
					$this->Admindao->updateData(['config'=>$config],'sys_plugins',
						array(
							array('key'=>'name','compare'=>'=','value'=>'\''.addslashes($segment).'\'')
						)
					);
					$config = json_decode($config,true);
					$config = is_array($config)?$config:[];
				}
				$data['content']=$segment.'.admin_config';
				$data['config']=$config;
				$this->load->view('template',$data);
			}
			else{
				redirect('Techsystem');
			}
		}
	}
	private function _downloadCore($username,$password){
		if(!file_exists('update')){
			mkdir('update',0777,true);
		}
		$file = 'update/tmp.zip';
		$fp = fopen ($file, 'w+');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://tech5s.com.vn/demo/analytics/Plugin/core');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(['username'=>$username,'password'=>$password]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		$result = curl_exec($ch);
		curl_close ($ch);
		if(file_exists($file)){
			$zip = new ZipArchive;
			$res = $zip->open($file);
			if ($res === TRUE) {
			  $zip->extractTo('update');
			  $zip->close();
			}
		}
	}
	private function _moveCores($folders){
        foreach ($folders as $k => $folder) {
            $this->_moveCore($folder[0],$folder[1]);
        }
    }
    private function _moveCore($from,$to){
        $this->_deleteDirectory($to);
        $this->_recurseCopy($from,$to);
    }
    private function _deleteDirectory($dirname,$exceptFiles =[],$deleteFolder = true) {
        if(is_array($dirname)){
            foreach ($dirname as $k => $dname) {
                $this->_deleteDirectory($dname,$exceptFiles);
            }
        }
        else{
            if (is_dir($dirname)) $dir_handle = opendir($dirname);
            if (!$dir_handle) return false;
            while( $file = readdir($dir_handle)) {
                if ($file != '.' && $file != '..' && !in_array($file, $exceptFiles)) {
                    if (!is_dir($dirname.'/'.$file)){
                        unlink($dirname.'/'.$file);
                    }
                    else{
                        $this->_deleteDirectory($dirname.'/'.$file,$exceptFiles);
                    }
                }
            }
            closedir($dir_handle);
            $isDirEmpty = !(new \FilesystemIterator($dirname))->valid();
            if($isDirEmpty && $deleteFolder){
                rmdir($dirname);
            }
            return true;
        }
    }
    private function _recurseCopy($src,$dst,$exceptFiles =[]) { 
        if(!file_exists($src))return;
        $dir = opendir($src); 
        if(!file_exists($dst)){
            mkdir($dst,0755,true); 
        }
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' ) && !in_array($file, $exceptFiles)) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->_recurseCopy($src . '/' . $file,$dst . '/' . $file,$exceptFiles); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }
    private function _updateCoreSql(){
    	$file = "update/db.sql";
    	if(file_exists($file)){
    		$content = file_get_contents($file);
	    	$contents = explode(";", $content);
	    	foreach ($contents as $k => $con) {
	    		if(strlen($con)==0) continue;
	    		$this->db->query($con);
	    	}
    	}
    	
    }
	public function updateCore()
    {
    	$this->testLoginAdmin();
    	if(!isAdminServer() && ENVIRONMENT !='development'){
    		redirect("/");
    	}
    	if ($this->input->server('REQUEST_METHOD') == 'GET')
		{
			$versions = file_get_contents('https://tech5s.com.vn/demo/analytics/Plugin/cate_info');
			$versions = json_decode($versions,true);
			$versions = @$versions?$versions:['version'=>0];

			$data['content']='nuy/update_core';
			$data['plugins']=$this->plugins;
			$data['versions']=$versions;
			$this->load->view('template',$data);
		}
		else{
			set_time_limit(0);
			$post = $this->input->post();
			$username = isset($post['username'])?$post['username']:'';
			$password = isset($post['password'])?$post['password']:'';
			if(strlen($username)>0 && strlen($password)>0){
				$this->_updateCoreSql();
				echo 'Download Core...<br>';
				$this->_downloadCore($username,$password);
		       	 $folders = [
			            ['update/public','public'],
			            ['update/system','system'],
			            ['update/theme/admin','theme/admin'],
			            ['update/theme/common','theme/common'],
			            ['update/application/helpers','application/helpers'],
			            ['update/application/hooks','application/hooks'],
			            ['update/application/core','application/core'],
			            ['update/application/libraries','application/libraries'],
			            ['update/application/language','application/language'],
			            ['update/application/models','application/models'],
			            ['update/application/modules','application/modules'],
			            ['update/vendor','vendor'],
			            ['update/application/controllers','application/controllers'],
			        ];
		       	echo 'Updating...<br>';
		       	$this->_moveCores($folders);
		       	$this->_deleteDirectory(['application/cache','application/config'],['.htaccess','config.php','database.php'],false);
		       	$this->_recurseCopy('update/application/config','application/config',['config.php','database.php']);
		        $this->_recurseCopy('update/application/plugins','application/plugins',['tmp.zip']);
		        unlink('.htaccess');
		        unlink('index.php');
		        copy('update/.htaccess','.htaccess'); 
		        copy('update/index.php','index.php'); 
		        $this->_deleteDirectory('update');
		       	echo 'Finish...<br>';
			}
			else{
				redirect('Techsystem/plugins');
			}
		}

    }
}