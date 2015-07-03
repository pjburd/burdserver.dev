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

class BURDShell_restore extends BURDShell_Plugin
{
    public $commands = array('svn',
                             'database'); 

    /*
     * @access public
     *
	 * @return	object
     */        
    public function help() 
    {
        $out = "";
		$out .= "backup list      : List known backups\n";
		$out .= "restore svn      : Restore repo backup for a project environment\n";
		$out .= "restore database : Restore database backup for a project environment\n";

        return $out;            
    }
	
	public function svn() 
	{
		if ($user_input = $this->validate_svn_project("backup"))
		{
            exec("mkdir ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
			$this->print_output($out_lines);
			
			//Create repo
		    exec(Config::$svn_bin_path."svnadmin create ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
			$this->print_output($out_lines);	 											     

			exec("gunzip -c ".Config::$backup_folder."/bk_svn_".$user_input.".gz | ".Config::$svn_bin_path."svnadmin load ".Config::$shell_folder."/svn/".$user_input, $out_lines);
			$this->print_output($out_lines);	 											     

			$this->svn_security($user_input);

			$this->print_line("Site repo restored.","i");

        }
    }
	public function database() 
	{    	
		if ($user_input = $this->validate_project(FALSE))
		{
        	// Uncompress backup
    		if (!file_exists(Config::$backup_folder."/bk_database_".$user_input.".sql.gz")) 
    		{
    			$this->print_line("[ERROR] backup ".Config::$backup_folder."/bk_database_".$user_input.".sql.gz does not exist.");	
    		}
    		else
    		{
    				
    		    exec("gzip -d ".Config::$backup_folder."/bk_database_".$user_input.".sql.gz", $out_lines);	    
    			$this->print_output($out_lines);						
    				
    		}
    		
    		$this->print_line("Looking for .sql file...");
    
    		if (!file_exists(Config::$backup_folder."/bk_database_".$user_input.".sql")) 
    		{
    			$this->print_line("[ERROR] backup ".Config::$backup_folder."/bk_database_".$user_input.".sql.gz does not exist.");	
    		}
    		else
    		{
    
    			if (Config::$db_admin_pass) 
    			{
    				$pass = Config::$db_admin_pass;
    			}
    			else
    			{
    				$pass = "";
    			}						
    								
    		
    		    exec("cat ".Config::$backup_folder."/bk_database_".$user_input.".sql | ".Config::$mysql_bin_path."mysql -u root -p".$pass." ".$user_input, $out_lines);	    
    			$this->print_output($out_lines);						
    
    			$this->print_line("site database restored.");											
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
    

	private function svn_security($user_input)
	{

		/*
			[/svn/PROJECTFOLDER/conf/svnserve.conf]
			anon-access = none
			auth-access = write
			password-db = passwd
		*/
		exec("sed -i .bk 's/^# auth\-access \= write/anon\-access \= none\\nauth\-access \= write/' ".Config::$shell_folder."/svn/".$user_input."/conf/svnserve.conf");
		exec("sed -i .bk 's/^# password\-db \= passwd/password\-db \= passwd/' ".Config::$shell_folder."/svn/".$user_input."/conf/svnserve.conf");

		/*
			[/svn/PROJECTFOLDER/conf/passwd]
			sysadmin = Password1
		*/
		$security_lines = Config::$shell_user." = ".Config::$shell_pass."\n";
		file_put_contents(Config::$shell_folder."/svn/".$user_input."/conf/passwd", $security_lines, FILE_APPEND | LOCK_EX);
		
		/*
			[/svn/PROJECTFOLDER/conf/authz]
			allaccess = sysadmin
			[/]
			@allaccess = rw
		*/							
		$security_lines = "allaccess = ".Config::$shell_user."\n[/]\n@allaccess = rw\n";
		file_put_contents(Config::$shell_folder."/svn/".$user_input."/conf/authz", $security_lines, FILE_APPEND | LOCK_EX);
		
		$this->print_line("Site repo security setup successfully.","i");

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
		$check_config_verbs = array('restore');
		                            
        $verb_text = "";
        switch($verb) {
            case 'restore':
                $verb_text = " to ".$verb;
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
				return $user_input;                
            }
        }
        return FALSE;
	}        
	    
}