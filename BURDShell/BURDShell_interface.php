<?php
/*
    BURDShell: Developer platform shell
    Copyright (C) 2014  Paul Burden

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
abstract class BURDShell_interface {

	/*
	 *@access	public	
	 *@var	string		String to show version of os
	*/
	public $os_version = "[No host os chosen]";
  
	/*
	 *@access	public	
	 *@var	boolean		Shell errors toggle, used for safety to prevent commands from running
	*/
	public $errors_found = FALSE;

	/*
	 *@access	protected	
	 *@var	string		Project name to use e.g. 'burdserver.dev'
	*/
	protected $project = '';

	/*
	 *@access	private	
	 *@var	array	User's directive command and args
	*/
	public $directive = array('command' => '', 'args' => '');

	/*
	 *@access	private	
	 *@var	array		Config settings for further interface support
	*/
/*
	public $config = array('db_admin_pass' => '',
							'backup_folder' => '/home/sysadmin/backups/',
							'app_folder' => '/home/sysadmin/BURDShell/apps');
*/
	    
	/*
	 * Set the network interface to be 'static'
	 *
	 *@access	public	
	 *@return	void
	*/
	abstract public function network_static();

	/*
	 * Set the network interface to be 'dynamic'
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function network_dynamic();
    
	/*
	 * Exec command to restart network
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function network_restart();

	/*
	 * Exec command to see network status
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function network_status();
    
	/*
	 * Exec command to fetch IP address
	 *
	 *@access	public	
	 *@return	string
	*/
	abstract public function get_ip_address();

	/*
	 * Exec command to see webserver status
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function webserver_status();
    
	/*
	 * Exec command to restart webserver
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function webserver_restart();

	/*
	 * Exec command to create a site webserver
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function site_create();
    
	/*
	 * Exec command to list known site configs
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function site_list();

	/*
	 * Exec command to list known svn repos
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_list(); 
       
	/*
	 * Display a list of svn help commands for a site repo
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_help();    

	/*
	 * Exec command to create an svn repo for a site project environment
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_create();
    
	/*
	 * Exec command to delete a svn repo for a site project environment
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_delete();
    
	/*
	 * Exec command to show history for a site project environment
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_history();
    
	/*
	 * Exec command to show revision for a site project environment
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_revision();

	/*
	 * Exec command to set up initial repo security for 'sysadmin'
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_security();   
    
	/*
	 * Exec command to List known users a svn repo
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_users();
    
	/*
	 * Exec command to List known permissions for a user in an svn repo
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_user();
    
	/*
	 * Exec command to List known permissiosn for a svn repo
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function svn_grants();   
    
	/*
	 * Exec command to test if database exists
	 *
	 *@access	public	
	 *@return	void
	*/
	abstract public function database_exists($db_name);


	/*
	 * Exec command to list known databases
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function database_list(); 
    
	/*
	 * Exec command to create a database for a site project environment
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function database_create();        

	/*
	 * Exec command to drop/delete a database for a site project environment
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function database_delete();        

	/*
	 * Exec command to list known apps
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function app_list(); 
    
	/*
	 * Exec command to install a  known app
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function app_install(); 
        
	/*
	 * Exec command to remove a  known app
	 *
	 *@access	public	
	 *@return	void
	*/
//!ENHANCEMENT to add app remove functionality
//    abstract public function app_remove(); 
        
	/*
	 * Exec command to list known backups
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function backup_list(); 
      
	/*
	 * Exec command to backup a site repo  (SVN dump, gzip -9)
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function backup_svn();  
     
	/*
	 * Exec command to restore a site repo backup (SVN dump, gzip -d)
	 *
	 *@access	public	
	 *@return	void
	*/
    abstract public function restore_svn();   

                
	/*
	 * Check shell required files environment
	 *
	 *@access	public	
	 *@param	boolean	If Shell->debug is TRUE then echo out files checked
	 *@return	array	An array of string errors  e.g. $errorsp[] = 'File not found'.
	*/
    abstract public function check_shell_env($debug=FALSE);

	/*
	 * Print array of string outs.  
	 * These arrays are produced with php function exec('somecommand' ,$outlines)
	 *
	 *@access	public	
	 *@param	array	 An array of strings to print out
	 *@param	boolean  Whether to hide comment lines or not.  Default is FALSE
	 *@return	void
	*/
	public function print_output($out_lines = array(), $hide_comments=FALSE) 
	{		
		foreach($out_lines as $out_line) 
		{
			if ($hide_comments == TRUE)
			{
				if (preg_match("/^(?![ \t]*#).*$/", $out_line))	// Only print line if it does not match a comment 
				{
					echo $out_line."\n";						
				}
			}
			else
			{
				echo $out_line."\n";	// Just spit it out
			}
		}
	}	
	
	/*
	 * Removes comments from out
	 *
	 *@access	public	
	 *@param	array	 An array of strings to print out
	 *@return	array
	*/
	public function tidy_flat_output($out_lines = array()) 
	{		
		$out_array = array();
		foreach($out_lines as $out_line) 
		{
			if (preg_match("/^(?![ \t]*#).*$/", $out_line))	// Only print line if it does not match a comment 
			{
				$out_array[] = $out_line;						
			}
		}
		return $out_array;
	}

	/*
	 * Print out a line 
	 *
	 *@access	public	
	 *@param	string	Line to print out
	 *@return	void
	*/	
	public function print_line($out_line) 
	{		
		echo $out_line."\n";			
	}

	/*
	 * Set project name for all commands
	 *
	 *@access	public	
	 *@param	string	Project name, if set then skip asking the question
	 *@return	void
	*/
	public function set_project($project="")
	{
		if (empty($project))
		{
			if (count($this->directive['args']) == 3)
			{
				$user_input = $this->directive['args'][2];
			}
			else
			{		
				echo "What is the project name? (Leave blank and press enter to reset)\n";
				$user_input = trim(fgets(STDIN));	
			}
		}
		else
		{
			$user_input = $project;
		}
		
		if (empty($user_input)) 
		{
			$this->project = '';
			$this->print_line("[INFO] Setting shell project to: 'empty'.");	
		} 
		else 
		{
			$this->project = $user_input;
			$this->print_line("[INFO] Setting shell project to: '".$user_input."'.");	

		}
	}


	/*
	 * Set user directive, helps functions identify other arguments
	 *
	 *@access	public	
	 *@param	array
	 *@return	void
	*/
	public function set_directive($directive)
	{
		$this->directive = $directive;
	}

	/*
	 * Set project name for all commands
	 *
	 *@access	public	
	 *@param	string	Project name
	 *@return	string
	*/
	public function get_project()
	{
		return $this->project;
	}
	
	/*
	 * Validate a file name
	 *
	 *@access	public	
	 *@param	string	File name to check
	 *@return	boolean
	*/		
	public function valid_filename($out_line) 
	{
		if (preg_match("/^[a-z0-9-.]+$/", $out_line)) 
		{
			return TRUE;
		}
		else
		{
			return FALSE;	// The file name can only contain "a-z", "0-9", "." and "-" and must be lower case
		}
	}
	
	public function line_exists($search_for_line, $path_to_file) 
	{
		$out_boolean = FALSE;
		$search = $search_for_line;
		$file = file($path_to_file);
		foreach($file as $line) 
		{
		    $line = trim($line);
		    if($line == $search) 
		    {
		    	$out_boolean = TRUE;
		        break;	    
		    }
		}
		
		return $out_boolean;
		
	}
	
	/*
	 * If $this->project is not set, prompt user a question which project to use.
	 *
	 *@access	public	
	 *@param	string	The question to show the user (e.g. "Which site domain to restore repo?")
	 *@return	string
	*/
	public function project_prompt($user_question)
	{
		$project = $this->get_project();
		if ($project)
		{
			$user_input = $project;
		}
		else
		{
			echo $user_question . "\n";
			$user_input = trim(fgets(STDIN));	
		}
		return $user_input;
	}

	/*
	 * Check is only index.html is in the only file in project folder
	 *
	 *@access	public	
	 *@param	string	Project to check
	 *@return	boolean
	*/
	public function is_project_empty($project)
	{	
		$ctr = 0;
		$found_index_file = FALSE;
		
		// Check to see if index.html exist in project
	
		
			// Count entries in project folder
			if ($handle = opendir(Config::$shell_folder."/sites/".$project."/public/"))
			{
				
				while (false !== ($entry = readdir($handle))) 
				{
					if ($entry != "." && $entry != "..")
					{
						$ctr++;
						if ($entry == 'index.html')
						{
							$found_index_file = TRUE;
						}
					}
				}
				closedir($handle);
			}
			
			if ($ctr == 1 && $found_index_file == TRUE)   // If only one file then it means, it is safe to delete index.html file
			{
				if (unlink(Config::$shell_folder."/sites/".$project."/public/index.html"))
				{
					return TRUE;
				}
			}
			
			if ($ctr == 0 && $found_index_file == FALSE)   // If no files found then its empty
			{
				return TRUE;
			}
			
			return FALSE; // Obveriosuly not empty
	}

	/*
	 * Verify app is a known installable app.
	 *
	 *@access	public	
	 *@param	string	App to verify
	 *@return	boolean
	*/
	public function allowed_app($app_name)
	{
		switch($app_name)
		{
			case "phpMyAdmin": return TRUE; break;
			case "websvn": return TRUE; break;
			default:
				return FALSE;
				break;
		}
	}

	/*
	 * Identify filename for the app
	 *
	 *@access	public	
	 *@param	string	App to verify
	 *@return	boolean
	*/
	public function get_app_files($app_name)
	{
		$out_array = array();
		
		if ($handle = opendir(Config::$app_folder)) 
		{
		// Find which software to install
			while (false !== ($entry = readdir($handle))) 
			{
				if (preg_match("/".quotemeta($app_name)."/", $entry))
				{
					$out_array[] = $entry;
				}
			}
		}
		return $out_array;
	}

	/*
	 * Verify if app has been installed
	 *
	 *@access	public	
	 *@param	string	App to verify
	 *@param	string	Project environment
	 *@return	boolean
	*/
	public function app_installed($app_name, $project)
	{
	// config.sample.inc.php	
		switch($app_name)
		{
			case "phpMyAdmin":
				// Find files 
					if (file_exists(Config::$shell_folder."/sites/".$project."/public/"."phpmyadmin.css.php"))
					{
						return TRUE;
					}
			case "websvn":
				// Find files 
					if (file_exists(Config::$shell_folder."/sites/".$project."/public/include/"."distconfig.php"))
					{
						return TRUE;
					}
				break;
		}
	}
	
	/*
	 * Generate random string of characters
	 *
	 *@access	public	
	 *@param	integer		Length of string
	 *@return	string
	*/
	function random_string($length = 10) 
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}
}
