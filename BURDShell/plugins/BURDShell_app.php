<?php
/*
    BURDShell: Developer platform shell
    Copyright (C) 2015  Paul Burden

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class BURDShell_app extends BURDShell_Plugin
{
    public $commands = array('list' => 'slist',
                             'install'); 
    

    /*
     * @access public
     *
	 * @return	object
     */        
    public function help() 
    {
    	$out = "";

		$out .= "app list    : List known apps or installed apps\n";
		$out .= "app install : Install an app for a site (aka project)\n";
//!ENHANCEMENT: Add feature to remove apps
//				$out .= "app remove  : Remove an app for a site (aka project)\n";
        $out .= "\n";
        return $out;            
    }
    

	public function slist() 
	{		 
	    exec("ls -l ".Config::$app_folder."/", $out_lines);	  
	    
        if (!empty($out_lines)) 
        {
            $this->print_line($out_lines);
        }
        else
        {
            $this->print_line("No apps found","i");
        }
	}	

	public function install() 
	{		 	    
		if ($user_input = $this->validate_project("install"))
		{
            if (count($this->directive['args']) == 3)
    		{
    			$app_name = $this->directive['args'][2];
    		}
    		else
    		{					
    			echo "Which app do you wish to install? (Currently only 'phpMyAdmin' and 'websvn')\n";
    			$app_name = trim(fgets(STDIN));	
    		}
    		
    		
    		if (!$this->allowed_app($app_name))
    		{
    			$this->print_line("app '".$app_name."' is not allowed.","e");	
    		}
    		else
    		{
    			$this->print_line("app '".$app_name."' is allowed.","i");							
    				
    			$app_versions = $this->get_app_files($app_name);
    			$total_app_versions = count($app_versions);
    			
    			if ($total_app_versions >= 2)
    			{
    				$this->print_line("Too many versions found for '".$app_name."' (Tip: Only need one)","e");			
    			}
    			
    			if ($total_app_versions == 1)
    			{
    				// Lets verify it has not been installed
    				if (!preg_match("/tar\.gz/", $app_versions[0]))
    				{
    					$this->print_line("App file name must be tar.gz for '".$app_name."'","e");			
    				}
    				else
    				{
    					//Verifiy if it installed
    					if ($this->app_installed($app_name, $user_input))
    					{
    						$this->print_line("App already installed '".$app_name."' (TIP: make sure '".$user_input."' is empty)","e");			
    					}
    					else
    					{
    						// If index.html exists lets remove this
    					
    						// Install app.											
    						//tar xzf archive.tar.gz -C /destination
    						
    						$this->print_line("Installing '".$app_name."'...","i");	
    										
    						if (!$this->is_project_empty($user_input))	// If index.html exists only lets remove this otherwise it is not empty
    						{
    							$this->print_line("You need to make sure you have removed index.html in '".$user_input."'","e");			
    						}
    						else
    						{
    							// Extract contents
    						    $out_lines = array();
    						    exec("tar xzf ".Config::$app_folder."/".$app_versions[0]." --strip-components 1 -C ".Config::$site_dir.$user_input."/public/", $out_lines);												
    						    $this->print_output($out_lines);
    						    											    
    						    // Change permission
    						    $out_lines = array();
    						    exec("chown -R ".Config::$shell_user. " ".Config::$site_dir.$user_input."/public/", $out_lines);											   
    						    $this->print_output($out_lines);
    						    
    						    $out_lines = array();
    						    exec("chgrp -R ".Config::$shell_group. " ".Config::$site_dir.$user_input."/public/", $out_lines);
    							$this->print_output($out_lines);
    
    						}
    						
    						// Based on app details, configure additional settings
    
    //ENHANCEMENT: Auto set up config.inc.php with 'blogfish_secret'											
    						switch ($app_name)
    						{
    							/*
    							case "phpMyAdmin":
    								// Set 'blowfish_secret'
    								$random_string = $this->random_string(32);
    								$tmp_file = Config::$site_dir.$user_input."/public/config.sample.inc.php";
    								$new_file = Config::$site_dir.$user_input."/public/config.inc.php";
    								
    								if (!file_exists($new_file) && 
    									file_exists($tmp_file))
    								{
    									exec("cp ".$tmp_file. " ".$new_file, $out_lines);
    									
    									// Edit new config file and update blowfish encryption key
    									
    									// Set permissions
    									exec("chown ".Config::$shell_user. " ".$new_file, $out_lines);
    									exec("chgrp ".Config::$shell_group. " ".$new_file, $out_lines);
    								}
    								break;
    							*/
    							case "websvn":
    									$tmp_file = Config::$site_dir.$user_input."/public/include/distconfig.php";
    									$new_file = Config::$site_dir.$user_input."/public/include/config.php";
    																					
    								// Copy include/distconfig.php to include/config.php
    								if (!file_exists($new_file) && 
    									 file_exists($tmp_file) &&
    									 file_exists(Config::$shell_folder.'/svn/'))
    								{
    									
    
    									exec("cp ".$tmp_file ." " . $new_file, $out_lines);
    								
    									if (is_writable($new_file)) {
    										$fp=fopen($new_file,"a");
    									    fwrite($fp,'$config->parentPath("'.Config::$shell_folder.'/svn/");');														
    										$this->print_line("[INFO] App '".$app_name."' : Created config file and set parentPath to '".Config::$shell_folder."/svn/'");			
    									    
    									    fclose($fp);
    									}
    
    										
    									exec("chown ".Config::$shell_user. " ".$new_file, $out_lines);
    									exec("chgrp ".Config::$shell_group. " ".$new_file, $out_lines);
    
    								}
    								else
    								{
    									$this->print_line("App '".$app_name."' : Make sure distconfig.php exists and config.php file does not exists within '".Config::$shell_folder."/svn/'","!");
    								}
    								break;													
    						}
    					
    						$this->print_line("App '".$app_name."' installed in '".$user_input."'","i");			
    
    					}
    				}
    			}
    		}
    		// Make sure the app has not already been installed
    		
    		//!TODO
    
    		
    		//$this->print_line("[INFO] creating app database '".$user_input."' ...");										
    		//$this->database_create();
    
		}
	}	

    /*
     * Validate a project
     *
	 * @param	string $verb                       Type of action to format question text
	 * @param	string $override_user_input        Input previous user's input      
     *
	 * @return	object
     */         
	private function validate_project($verb, $override_user_input="") 
	{    	 
		
		$check_config = FALSE;
		$check_config_verbs = array('install');
		                            
        $verb_text = "";
        switch($verb) { 
            case 'install':                
                $verb_text = " to ".$verb." app";
                break;
        }

		$user_input = $this->project_prompt("Which site" .$verb_text ."?", $override_user_input);
		
		if (in_array($verb, $check_config_verbs))
		{
    		$check_config = TRUE;
		}
		
		if (empty($user_input)) 
		{
			$this->print_line("Site domain name missing.","e");	
		} 
		else 
		{
			$this->print_line("Validating site...","i");
			
			if (!$this->valid_filename($user_input)) 
			{
				$this->print_line("The file name can only contain \"a-z\", \"0-9\", \".\" and \"-\" and must be lower case","e");
			}
			else
			{ 
				if (in_array($user_input, array("localhost","default","default-ssl"))) 
				{
					$this->print_line("You cannot use '".$user_input."', this is a default web server virtualhost."."e");
				} 
				else 
				{ 
					return $user_input;
                }
            }
        }
        return FALSE;
	}        
}