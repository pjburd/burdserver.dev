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

class BURDShell_site extends BURDShell_Plugin
{
    public $commands = array('help' => 'site_help',
                             'list' => 'slist',
                             'create',
                             'delete' => 'sdelete'); 

    /*
     * @access public
     *
	 * @return	object
     */        
    public function help() 
    {
		$out .= "site list   : List known site virtualhost configuration files\n";
		$out .= "site create : Create a new site (aka project)\n";
		$out .= "site delete : Delete a site (aka project)\n";
		$out .= "site help   : Shows helpful echo commands for re-enabling site in hosts file\n";

        return $out;            
    }

	public function site_help()
	{
		if ($user_input = $this->validate_project()) 
		{
			$this->print_line("echo \"".$this->ipa()."  ".$user_input."\" >> /etc/hosts");
			$this->print_line("OR");
			$this->print_line("echo \"127.0.0.1  ".$user_input."\" >> /etc/hosts");
        }
	}
	
	public function slist() 
	{		 
	    exec("ls -l " . Config::$virtualhost_dir, $out_lines);	  
	    
        if (!empty($out_lines)) 
        {
            $this->print_line($out_lines);
        }
        else
        {
            $this->print_line("No sites found","i");
        }
	}	

	public function create()
	{
		if ($user_input = $this->validate_project()) 
		{
    		
    		$this->print_line("Creating site domain '".$user_input."'...");			
    
        	switch (Config::$shell_os)
        	{
            	case 'Ubuntu':
            	case 'OSx':
            	        						
        			//Read template into variable
        			$template = file_get_contents(Config::$shell_folder.'/BURDShell/templates/osx-apache2-virtualhost.txt');
        
        			// Change template key names
        			$template = preg_replace("/\[DOMAINNAME\]/", $user_input, $template);	
        			$template = preg_replace("/\[SITE\_FOLDER\]/", Config::$site_dir, $template);	
        						
        			// Save file
        			file_put_contents( Config::$virtualhost_dir.$user_input.".conf", $template);		// Mac requires .conf at end
        			
        			// Set up default structure						
        			if (file_exists(Config::$site_dir.$user_input)) 
        			{
        				$this->print_line("Site folder '".$user_input."' exists","i");			
        			}
        			else
        			{
        				$this->print_line("Creating site folder '".$user_input."' scaffold...");
        				
        				$this->print_output($this->admin_exec("mkdir ".Config::$site_dir.$user_input, FALSE));
        				$this->print_output($this->admin_exec("mkdir ".Config::$site_dir.$user_input."/public", FALSE));
        				$this->print_output($this->admin_exec("mkdir ".Config::$site_dir.$user_input."/private", FALSE));
        											
        				$template = file_get_contents(Config::$shell_folder.'/BURDShell/templates/site-index-file.txt');
        				$template = preg_replace("/\[PROJECT\]/", $user_input, $template);						
        							
        				file_put_contents(Config::$site_dir.$user_input."/public/index.html", $template);
        				
        				$this->print_output($this->admin_exec("chown -R ".Config::$shell_user." ".Config::$site_dir.$user_input, FALSE));
        				$this->print_output($this->admin_exec("chgrp -R ".Config::$shell_group." ".Config::$site_dir.$user_input, FALSE));
        					
        			}        			
        			break;
            }
        
            // Enable server
			$this->print_output($this->admin_exec("apachectl graceful"));						

			$this->print_line("Site created","i");
			$this->print_line("Update host machine /etc/hosts file to make site available.","!");
			$this->print_line("Run the following:");						
			$this->print_line("echo \"".$this->ipa()."  ".$user_input."\" >> /etc/hosts");
			$this->print_line("OR");
			$this->print_line("echo \"127.0.0.1  ".$user_input."\" >> /etc/hosts");
			
    		
        }
	}
	
	
	public function sdelete()
	{    	
		if ($user_input = $this->validate_project(FALSE)) 
		{
    		if (!file_exists(Config::$virtualhost_dir.$user_input.".conf"))
    		{
                $this->print_line("Site does not exist","i");
            }
            else
            {
                //Delete config						
                $this->print_output($this->admin_exec("rm ". Config::$virtualhost_dir.$user_input.".conf", FALSE));
                
                //Restart webserver gracefully
                $this->print_output($this->admin_exec("apachectl graceful"));
                
                //reload server
                $this->print_line("Site deleted","i");
                $this->print_line("Update host machine /etc/hosts file to remove redundant site.","!");
            }
        }
	}

    /*
     * Validate a project
     *
	 * @param	boolean $config_test      Include config test
     *
	 * @return	object
     */     
	private function validate_project($config_test=TRUE) 
	{			
		$user_input = $this->project_prompt("What is the site domain?");
	
		if (empty($user_input)) 
		{
			$this->print_line("site domain name missing.","e");	
		} 
		else 
		{
			
			$this->print_line("Validating site...");
			
			if (!$this->valid_filename($user_input)) 
			{
				$this->print_line("The file name can only contain \"a-z\", \"0-9\", \".\" and \"-\" and must be lower case","e");
			}
			else
			{
				if (in_array($user_input, array("localhost","default","default-ssl"))) 
				{
					$this->print_line("You cannot use '".$user_input."', this is a default web server virtualhost.","e");
				} 
				else 
				{
    				//Make sure the file exists
					if ($config_test && file_exists( Config::$virtualhost_dir.$user_input.".conf")) 
					{
						$this->print_line("site domain '".$user_input."' already exists. Run 'webserver sites' for further info.","e");    					
    				}
    				else
    				{
        				return $user_input;        				
    				}
                }
            }
        }
        return FALSE;
    }
    
	private function ipa() {
    	$out = "";
    	switch (Config::$shell_os)
    	{
        	case 'Ubuntu':
        		 $out = exec("/sbin/ifconfig " . Config::$network_interface . " | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");		 
        	    break;
            case 'OSx':
        		 $out = exec("ipconfig getifaddr ". Config::$network_interface);
                break;        	
    	}

		 return $out;
	}
	    
}