<?php
if (!class_exists('Update_LeadGenerator_WP')) {
	class Update_LeadGenerator_WP {
        protected $respository_url = "https://githubversions.optimizaclick.com/repositories/view/";
		protected $temp_name = "temp-leadgenerator.zip";
		protected $main_file = "leadgenerator.php";
		protected $url_main_file = "leadgenerator/leadgenerator.php";
		protected $url_update = "update-leadgenerator";
		protected $url_version = "version-leadgenerator";
		protected $install_plugin_url = "leadgenerator_install";

        public function cron_update_leadgenerator() {
                if (! wp_next_scheduled ( 'auto_update_leadgenerator' )) 
                    wp_schedule_event(time(), 'daily', 'auto_update_leadgenerator');	
                 }

        public function disable_cron_update_leadgenerator() {
			wp_clear_scheduled_hook('auto_update_leadgenerator');
		}


		public function show_version() {
			if( basename($_SERVER['REQUEST_URI']) == $this->url_version) 
			{
				echo $this->get_version_plugin();	
				
				exit();
			}
		}

        public function auto_update_plugin() {
            $link = $this->get_repository_values("url");
				
			if(strpos($_SERVER['REQUEST_URI'], "/wp-admin/") === false)	{
				$file = "./wp-content/plugins/".$this->temp_name;	
				$dir = "./wp-content/plugins/";
			} else {		
				$file = "../wp-content/plugins/".$this->temp_name;
				$dir = "../wp-content/plugins/";
			}
				
			file_put_contents($file, fopen($link, 'r'));
				
			$zip = new ZipArchive;
				
			if ($zip->open($file) === TRUE) {
				$zip->extractTo($dir);
				$zip->close();
			} 
				
			unlink($file);
		}

        public function get_repository_values($data) {	
			$content = file_get_contents($this->respository_url);
			
			$values = explode("|", $content);
			
			if($data == "version")
				return $values[0];
			else
				return $values[1]; 
		}
        
        public function force_update() {
			if( basename($_SERVER['REQUEST_URI']) == $this->url_update) 
			{
				$this->auto_update_plugin();	
				
				wp_redirect(get_home_url()."/".$this->url_version);
				
				exit();
			}
		}
    }
new Update_LeadGenerator_WP();
}		