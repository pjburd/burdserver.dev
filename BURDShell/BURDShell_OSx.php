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
class BURDShell_OSx {

	public $os_version = "OSX 10.9";    // Works in Yosemite also
/*
	public function restore_svn() 
	{		 
		$user_input = $this->project_prompt("Which site to restore repo?");
		
		if (empty($user_input)) 
		{
			$this->print_line("[ERROR] site domain name missing.");	
		} 
		else 
		{
			
			$this->print_line("Validating site...");
			
			if (!$this->valid_filename($user_input)) 
			{
				$this->print_line("[ERROR] The file name can only contain \"a-z\", \"0-9\", \".\" and \"-\" and must be lower case");
			}
			else
			{
				if (in_array($user_input, array("localhost","default","default-ssl"))) 
				{
					$this->print_line("[ERROR] You cannot use '".$user_input."', this is a default web server virtualhost.");
				} 
				else 
				{
					$this->print_line("Making site repo...");					
					if (file_exists(Config::$shell_folder."/svn/".$user_input))
					{
						$this->print_line("[ERROR] Site repo already exists.");
					} 
					else
					{
					    exec("mkdir ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
						$this->print_output($out_lines);
						
						//Create repo
					    exec(Config::$svn_bin_path."svnadmin create ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
						$this->print_output($out_lines);	 											     
	
						exec("gunzip -c ".Config::$backup_folder."/bk_svn_".$user_input.".gz | ".Config::$svn_bin_path."svnadmin load ".Config::$shell_folder."/svn/".$user_input, $out_lines);
						$this->print_output($out_lines);	 											     
	
						$this->svn_security($user_input);
	
						$this->print_line("Site repo restored.");
					}
				}
			}
		}
	}
	
	public function restore_database() 
	{		 
		$user_input = $this->project_prompt("Which site to restore database?");
		
		if (empty($user_input)) 
		{
			$this->print_line("[ERROR] site domain name missing.");	
		} 
		else 
		{
			
			$this->print_line("Validating site...");
			
			if (!$this->valid_filename($user_input)) 
			{
				$this->print_line("[ERROR] The file name can only contain \"a-z\", \"0-9\", \".\" and \"-\" and must be lower case");
			}
			else
			{
			
				if (in_array($user_input, array("information_schema","mysql","performance_schema","test"))) 
				{
					$this->print_line("[ERROR] You cannot use '".$user_input."', this is a default database system requirement.");
				} 
				else 
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
		}
	}	
*/
	public function write_check()
	{
		$errors = array();		

		$files = array();

		// Folders that will be required to be writable		
		$files[] = Config::$shell_folder."/svn/";
		$files[] = Config::$site_dir;
		$files[] = Config::$shell_folder."/vhosts/";
		$files[] = Config::$backup_folder."/";
	
		foreach($files as $file) 
		{
		    if (!is_writable($file)) 
		    {
			    $errors[] = "[WARNING] ".$file." is not writable.";
		    }
	    }
	    
		return $errors;
	}

	public function check_shell_env($debug=FALSE) 
	{
		$errors = array();		
		$files = array();
		
		
		//System environment requirements
		$files[] = "/etc/apache2/";
		
		if (Config::$virtual_machine == TRUE)
		{
//Not allowed in OSx environment - Maybe different
//			$files[] = "/etc/network/interfaces";
//			$files[] = "/etc/network/interfaces.static";
//			$files[] = "/etc/network/interfaces.original";
//			$files[] = "/etc/network/interfaces.dynamic";	
		}
		
		//Folders
		$files[] = Config::$shell_folder."/svn/";
		$files[] = Config::$site_dir;
		$files[] = Config::$shell_folder."/vhosts/";
		$files[] = Config::$backup_folder."/";
		$files[] = Config::$shell_folder."/BURDShell/apps/";
		$files[] = Config::$shell_folder."/BURDShell/templates/";
				
		//BURDShell templates
		$files[] = Config::$shell_folder."/BURDShell/templates/osx-apache2-virtualhost.txt";
		$files[] = Config::$shell_folder."/BURDShell/templates/site-index-file.txt";

		if ($debug)
		{
			echo "Files n folders checked are:";
			print_r($files);
		}
				
		foreach($files as $file) 
		{
		    if (!file_exists($file)) 
		    {
			    $errors[] = $file." does not exist.";
			}
		}
		
		if (count($errors)) 
		{
			$this->errors_found = TRUE;
		}
		return $errors;
	}
}