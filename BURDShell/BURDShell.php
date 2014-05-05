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

class BURDShell 
{
		 
	/*
	 * @var	object	Instant of shown os shell system that gets initalized in construct
	 */
	 private $shell;
	 
	/*
	 * @var	object	Version number for shell
	 */
	 private $shell_version = '1.1.1b';
	 
	/*
	 * @var	object	Instant of shell os type
	 */
	 private $shell_os = '';

	/*
	 * @var	boolean		Set shell in debug mode
	 */
	 private $debug = FALSE;

	 private function print_license($section)
	 {
		 switch($section)
		 {
		 	case 'copyright':
				echo "\nBURDShell: Developer platform shell  Copyright (C) 2014  Paul Burden";
				echo "\nThis program comes with ABSOLUTELY NO WARRANTY; for details type `shell w'.";
				echo "\nThis is free software, and you are welcome to redistribute it";
				echo "\nunder certain conditions; type `shell c' for details.\n";
		 		break;
			 case 'warranty':
			 	echo "\nThis program is distributed in the hope that it will be useful,";
			 	echo "\nbut WITHOUT ANY WARRANTY; without even the implied warranty of";
			 	echo "\nMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the";
			 	echo "\nGNU General Public License for more details.\n";
			 	break;			 
			 case 'conditions':
			 	echo "\nYou understand that a password prompt may reveal your password.\n";
			 	break;
		 }
	 }

    /*
     *@access public
     *@param	string	Optional project to use
     *@param	string	Type of operating system for shell host.  Currently only 'Ubuntu'
     */
    public function __construct($project="", $shell_os="")
    {
				
		$this->debug = Config::$debug;
		$this->shell_os = $shell_os;
				
		$this->print_license('copyright');
		
		echo "\n";
    	        
        // Iinit shell
        if (!empty($shell_os)) 
        {
	        $object_name = "BURDShell_".$shell_os;        
	        $this->shell = new $object_name;
	        
	        
	        // Set desired project
	        if ($project)
	        {
		        $this->shell->set_project($project);
	        }
	        
	        //Print any shell issues
	        $errors = $this->shell->check_shell_env($this->debug);	  

			// Set working directory so not errors occur
			chdir (Config::$shell_folder);
			echo "[INFO] Using config for  : ".$shell_os."\n";
			echo "[INFO] Working directory : ".getcwd()."\n";
			echo "[INFO] BURDShell version : ".$this->shell_version."\n";	// Print BURDShell version

			// Check
			if (posix_getuid() != 0)
			{
				// Should the user bypass the check for sudo bash, warn them.
				echo "[ERROR] If you run BURDShell as non root, it can reveal passwords. TIP 'sudo bash' once.";
			}

				        
	        if (count($errors)) 
	        {
		        $this->errors_found = TRUE;
	        }
	              
	        $this->report_error($errors);
	        echo "\n";
	        
	        $this->start_main_menu();
        } 
        else 
        {
	        echo "[ERROR] You must set your desired host os.  Currently only 'Ubuntu'.";
        }
        
    }

	/*
	 * Prints out any known errors
	 *
	 *@access	public	
	 *@param	array	List of errors formatted with key name as the error type
	 *@return	void
	*/
    public function report_error($errors) {
    	if (count($errors)) 
    	{
    		echo "\n[ERROR] Errors found:\n";
		    foreach($errors as $error) 
		    {
			    echo "\n".$error;
		    }
	    }
    }    
    
    private function print_menu($menu_type = "main") {
    	$title = "Help [".$menu_type."]";
    	 
        echo "\n".$title;
        echo "\n";
        for($i=0; $i<strlen($title); $i++) 
        {
	        echo "-";
        }
            
    	switch($menu_type) 
    	{
    		case "debug":
				echo "\nshell debugoff : Switch off shell debug";
				break;
	    	case "main":
		        echo "\nsite      : Show site management commands";
		        echo "\nwebserver : Show webserver management commands";
				echo "\nnetwork   : Show network management commands";
				echo "\nsvn       : Show svn management commands";
				echo "\ndatabase  : Show database management commands";
				echo "\napp       : Show app management commands";
				echo "\nbackup    : Show backup management commands";
				echo "\nrestore   : Show restore backup management commands";
				echo "\nshell     : Show shell management commands";
				echo "\nversion   : Print version of BURDShell";
		        echo "\nquit      : Exit BURDShell";
		        echo "\n";
	    		break;
	    	case "network":
				if (Config::$virtual_machine == TRUE)
				{
					echo "\nnetwork static  : Set network interface to be static IP";
					echo "\nnetwork dynamic : Set network interface to be dynamic IP";
				}
				echo "\nnetwork status  : Show network interface details";
		        echo "\n";
	    		break;
	    	case "webserver":
				echo "\nwebserver sites   : Show a list known sites enabled on webserver";
				echo "\nwebserver status  : Show status of web server";
				echo "\nwebserver restart : Restart web server";
		        echo "\n";
	    		break;
	    	case "site":
				echo "\nsite list   : List known site virtualhost configuration files";
				echo "\nsite create : Create a new site (aka project)";
				echo "\nsite delete : Delete a site (aka project)";
				echo "\nsite help   : Shows helpful echo commands for re-enabling site in hosts file";
		        echo "\n";
	    		break;
	    	case "app":
				echo "\napp list    : List known apps or installed apps";
				echo "\napp install : Install an app for a site (aka project)";
//!ENHANCEMENT: Add feature to remove apps
//				echo "\napp remove  : Remove an app for a site (aka project)";
		        echo "\n";
	    		break;
	    	case "svn":
				echo "\nsvn list        : List known svn repos";
				echo "\nsvn help        : Show svn commands for a project environment";
				echo "\nsvn create      : Create a new repo for a project environment";
				echo "\nsvn delete      : Delete a repo for a project environment";
				echo "\nsvn history     : View repo history for a project environment";
				echo "\nsvn revision    : View repo revision for a project environment";
				echo "\nsvn log         : View repo log for a project environment";
//Hidden as we automatically do this when  shell commands 'svn create' and 'restore svn' are executed
//				echo "\nsvn security : Set up security for a site repo project environment";
				echo "\nsvn user        : List user permissions for a project environment";
				echo "\nsvn users       : List users for a project environment";
				echo "\nsvn grants      : List permissions for a project environment";

				echo "\nsvn serve       : Start SVN service deamon";
		        echo "\n";
	    		break;
	    	case "backup":
				echo "\nbackup list     : List known backups";
				echo "\nbackup svn      : Backup repo for a project environment";
				echo "\nbackup database : Backup database for a project environment";
		        echo "\n";
	    		break;
	    	case "database":
				echo "\ndatabase list   : List known databases";
				echo "\ndatabase create : Create a database for a project environment";
				echo "\ndatabase delete : Drop a database for a project environment";
		        echo "\n";
	    		break;
	    	case "restore":
				echo "\nbackup list      : List known backups";
				echo "\nrestore svn      : Restore repo backup for a project environment";
				echo "\nrestore database : Restore database backup for a project environment";
		        echo "\n";
	    		break;
	    	case "shell":
				echo "\nshell debugon  : Switch on shell debug";
				echo "\nshell debugoff : Switch off shell debug";
				echo "\nshell config   : Show config settings";
				echo "\nshell project  : Set the desired project to work with.";
				echo "\nshell os       : Print current operating system module in use";
				echo "\nshell w	       : Show warranty information";
				echo "\nshell c	       : Show conditions of use information";
		        echo "\n";
	    		break;
    	}
        echo "\n";    	
    }
    
    public function start_main_menu()
    {
        
    	// Show main menu
    	$user_input = "";

		$this->echo_prompt();			

		
		$user_input = trim(fgets(STDIN));	    
	    	    
		while ($user_input != "quit") 
		{
		    $this->is_safe_env();
	        
	        $directive = $this->fetch_directive($user_input);
	        	        
			if ($this->debug == TRUE)
			{	        
				print_r($directive);
				
				
			    switch ($directive['command']) 
			    {
			    	case "help":
			    		$this->print_menu('debug');
			    		break;		
					case "shell debugoff":
						$this->debug = FALSE;
						break;	
			    }
			}
			else 
			{
				$this->shell->set_directive($directive);	//Set the directive found
				
				// Attempt to find function to run
				$func_name = preg_replace("/ /", "_", $directive['command']);				
				if(is_callable(array($this->shell, $func_name))) 
				{
					 $this->shell->$func_name();
				}
				else
				{
					// Try and look for a default command
				    switch ($directive['command']) 
				    {
				    	case "help":
				    		$this->print_menu();
				    		break;
				    		
						//help commands
					    case "network":
					    case "webserver":
					    case "site":
					    case "app":
					    case "svn":
					    case "database":
					    case "backup":
					    case "restore":
					    case "shell":
					    	$this->print_menu($directive['command']);
					    	break;
		
						//Basic commands
						case "shell os":
							$this->shell->print_line($this->shell->os_version);  // Print version of OS module
							break;						
						case "shell project":
							$this->shell->set_project();
							break;
						case "version":
							$this->shell->print_line($this->shell_version);	// Print BURDShell version
							break;					
				
						//Shell debug commands
						case "shell debugon":
							$this->shell->print_line("Debug mode to see directives and parameters only.");
							$this->shell->print_line("Type 'shell debugoff' to finish debug session.");
							$this->debug = TRUE;
							break;		
						case "shell config":

							$config_obj = 'Config';
							$my_class = new $config_obj;

							$class_vars = get_class_vars(get_class($my_class));
							
							foreach ($class_vars as $name => $value) {
							    echo "$name : $value\n";
							}

						
							break;
						// Shell warranty and conditions
						case "shell w":
							$this->print_license('warranty');
							break;
						case "shell c":
							$this->print_license('conditions');
							break;
							
						//Unknown commands			    	
					    default:			    
					    	if ($user_input != "") 
					    	{
						    	echo "\nCommand not recognised.\n";
						    }
					    	break;
				    }
						
				}
			}		    
		    $project = $this->shell->get_project();
		    
			$this->echo_prompt();
		    $user_input = trim(fgets(STDIN));
		    
		}
		echo "\nHave a nice day :)\n";
    }


	/*
	 * Echo the desired prompt
	 *
	 *@access	private	
	 *@return	void
	*/
	private function echo_prompt()
	{
	    $project = $this->shell->get_project();
	    $debug_title = "";
	    
	    if ($this->debug == TRUE) 
	    {
		    $debug_title = "(debug)";
	    }
		if ($project)
		{
			echo "BURDShell [" .$project . "]".$debug_title.">";
		}
		else
		{
			echo "BURDShell ".$debug_title.">";        
		}
	}
	
	/*
	 * Fetch user directive's command and arguments
	 *
	 *@access	public	
	 *@param	string		User input
	 *@return	array
	*/
    public function fetch_directive($user_input) 
    {
    	$out_array = array();
		$command = "";	        // Default no command found
    	$args = array();
    	
    	if ($user_input)
    	{
    		$user_input = preg_replace('!\s+!', ' ', $user_input);	// Remove multiple spaces
	        
	        $args = explode(" ", $user_input);		// args found in array
	        			
	        if (is_array($args))
	        {
	        	if (count($args) >= 2)
	        	{
			        $command = $args[0]." ".$args[1];	// We only care about the first two args for command
			    }
			    else
			    {
			        $command = $args[0];

			    }
	        }
    	}    
    	
    	$out_array['command'] = $command;
    	$out_array['args'] = $args;
    	
    	return $out_array;
    }
	
	/*
	 * Check to see if shell commands is safe to run.  
	 * If errors found then shell dies to prevent command from ruunning.
	 *
	 *@access	public	
	 *@return	void
	*/
    public function is_safe_env() 
    {
		if ($this->shell->errors_found) {
			echo "\n[NOTICE]Cannot run any commands until shell errors fixed.\n";
			die;
		}    
    }

    private function c_print($colour, $message)
    {
//!TODO: Create colored shell lines        
    }
}
