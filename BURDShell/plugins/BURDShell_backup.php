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

class BURDShell_backup extends BURDShell_Plugin
{
    public $commands = array('list' => 'slist',
                             'svn',
                             'database'); 

    /*
     * @access public
     *
	 * @return	object
     */        
    public function help() 
    {
        $out = "";
		$out .= "backup list     : List known backups\n";
		$out .= "backup svn      : Backup repo for a project environment\n";
		$out .= "backup database : Backup database for a project environment\n";

        return $out;            
    }
	
	public function slist() 
	{		 
	    exec("ls -l ".Config::$backup_folder."/", $out_lines);	  
	    
        if (!empty($out_lines)) 
        {
            $this->print_line($out_lines);
        }
        else
        {
            $this->print_line("No backups found","i");
        }
	}
	
	public function svn() {
    	
		if ($user_input = $this->validate_svn_project("backup"))
		{
            ////svnadmin dump -q /path/to/repo | bzip2 -9 > filename.bz2
    		$this->print_line("Creating svn backup...");	
    	    exec(Config::$svn_bin_path."svnadmin dump -q ".Config::$shell_folder."/svn/".$user_input." | gzip -9 > ".Config::$backup_folder."/bk_svn_".$user_input.".gz", $out_lines);	   
    		$this->print_output($out_lines);	 
    		
    		$this->print_line("Site svn repo backup created. Check ".Config::$backup_folder."/","i");
        }
    }
	public function database() {
    	
		if ($user_input = $this->validate_project(FALSE))
		{
            $this->print_line("Creating database backup...");	
    
    		if (Config::$db_admin_pass) 
    		{
    			$pass = Config::$db_admin_pass;
    		}
    		else
    		{
    			$pass = "";
    		}						
    		
    		// Dump database
    	    exec(Config::$mysql_bin_path."mysqldump -u root -p".$pass." ".$user_input." > ".Config::$backup_folder."/bk_database_".$user_input.".sql", $out_lines);	   
    		$this->print_output($out_lines);	 
    
    		// Compress backup
    	    exec("gzip -9f ".Config::$backup_folder."/bk_database_".$user_input.".sql", $out_lines);	   
    		$this->print_output($out_lines);	 
    		
    		$this->print_line("Site database backup created. Check ".Config::$backup_folder."/","i");
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
    


    /*
     * Validate a project
     *
	 * @param	string $verb                       Type of action to format question text
	 * @param	string $override_user_input        Input previous user's input      
     *
	 * @return	object
     */         
	private function validate_svn_project($verb, $override_user_input="") 
	{    	 
		
		$check_config = FALSE;
		$check_config_verbs = array('backup');
		                            
        $verb_text = "";
        switch($verb) {
            case 'backup':
                $verb_text = " to backup ".$verb;
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
					if ($check_config == TRUE && !file_exists(Config::$shell_folder."/svn/".$user_input)) 
					{ 
						$this->print_line("site project repo does not exist.","e");	
					}
					elseif ($verb == "create" && file_exists(Config::$shell_folder."/svn/".$user_input)) 
					{ 
						$this->print_line("site project repo does exits exist.","i");	
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
	    
}