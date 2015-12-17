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

class BURDShell extends BURDShell_interface
{
		 
	/*
	 * @var	object	Instant of shown os shell system that gets initalized in construct
	 */
	 private $shell;

	/*
	 * @var	array    Plugins loaded
	 */
	 private $plugins = array();
	 	 

	/*
	 * @var	array    Command tree
	 */
     private $command_tree = array();
	 	 
	/*
	 * @var	object	Version number for shell
	 */
	 private $shell_version = '2.1.2';
	 
	/*
	 * @var	object	Instant of shell os type
	 */
	 private $shell_os = '';

	/*
	 * @var	object	   System object which includes plugins
	 */
	 private $system = FALSE;
	 
	 
	/*
	 * @var	boolean		Set shell in debug mode
	 */
	 private $debug = FALSE;

    /*
     *@access public
     *@param	array	Plugins to load
     *@param	string	Optional project to use
     *@param	string	Type of operating system for shell host.  Currently only 'Ubuntu'
	 *@return	void
     */
    public function __construct($plugins, $project="", $shell_os="")
    {
				
		$this->debug = Config::$debug;
		$this->shell_os = $shell_os;
				
        // Register plugins
        foreach($plugins as $plugin_name => $plugin_object) {
            if (is_object($plugin_object)) {                
                $this->plugins[]  = $plugin_name;
                $this->{$plugin_name} = $plugin_object;
                
                $this->command_tree[$plugin_name] = $this->{$plugin_name}->get_commands();

            }
        }
				
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
		        $this->set_project($project);
	        }
	         

			// Set working directory so not errors occur
			chdir (Config::$shell_folder);
			echo "[INFO] Using config for  : ".$shell_os."\n";
			echo "[INFO] Working directory : ".getcwd()."\n";
			echo "[INFO] BURDShell version : ".$this->shell_version."\n";	// Print BURDShell version

	        //Print any shell issues
	        $errors = $this->shell->check_shell_env($this->debug);	 				        
	        if (count($errors)) 
	        {
		        $this->errors_found = TRUE;
	        }	             
	            
	        $this->report_error($errors);

			// Find any warnings that we may need to address
	        echo "\n";	        
	        $warnings = $this->shell->write_check();
	        $this->print_output($warnings);
	        if (!empty($warnings))
	        {
				echo "[TIP] You may need to either 'sudo' the shell command e.g. 'sudo shell.php' or check your config settings for ".$shell_os."\n";
	        }
	        
	        echo "\n";
	        
	        $this->start_main_menu();
        } 
        else 
        {
	        echo "[ERROR] You must set your desired host os.  Currently only 'Ubuntu' and 'OSx'.";
        }
        
    }

	/*
	 * Prints out any known errors
	 *
	 *@access	public	
	 *@param	array	List of errors formatted with key name as the error type
	 *@return	void
	*/
    private function print_license($section)
    {
        switch($section)
        {
            case 'copyright':
                echo "\nBURDShell: Developer platform shell  Copyright (C) 2015  Paul Burden";
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
                echo "\nYou understand and respect your system environment.\n";
                break;
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

        if ($menu_type == "main")
        {
            echo "\nCommands (Type a command for additional help)\n";
            echo "---------------------------------------------";
         }   
    	switch($menu_type) 
    	{
    		case "debug":
				echo "\nshell debugoff : Switch off shell debug";
				break;
	    	case "main":
	    	    foreach($this->plugins as $plugin_name)
	    	    {
    	    	    echo "\n".$plugin_name;
	    	    }
				echo "\nshell";
				echo "\nversion";
		        echo "\nquit";
		        echo "\n";
	    		break;    	    		
	    	case "shell":
				echo "\nshell debugon  : Switch on shell debug";
				echo "\nshell debugoff : Switch off shell debug";
				echo "\nshell config   : Show config settings";
				echo "\nshell plugins  : List loaded plugins";
				echo "\nshell tree     : List all known commands";
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
				$this->set_directive($directive);	//Set the directive found

				// Attempt to find function to run
				$func_name = preg_replace("/ /", "_", $directive['command']);				
				if(is_callable(array($this->shell, $func_name)))    // Is internal command?
				{
					 $this->$func_name();
				}
				elseif(!empty($directive['args']) 
				        && !empty($this->command_tree[$directive['args'][0]])) // Is plugin command?
				{
    			     // Run plugin
                    if (count($directive['args']) == 1) // Show help for this command 
                    {
                        // show help
					    $this->print_line( $this->{$directive['args'][0]}->help() );  
                    }
                    else
                    {                        
                        // Verify it is not an alias command                        
                        $plugin_class = $directive['args'][0];
                        $plugin_func_chk = $directive['args'][1];
                        
                        if (isset($this->command_tree[$plugin_class][$plugin_func_chk])) 
                        {                            
                            $plugin_function = $this->command_tree[$plugin_class][$plugin_func_chk];    // Alias found
                        }
                        else
                        {
                            $plugin_function = $directive['args'][1];
                        }

                        // Prep plugin environment to match shell                        
                        $this->{$plugin_class}->set_directive($directive);	//Set the primary directive found for plugin to assist with the requested function                        
                        $chk_project = $this->get_project();    
                        
                        if (!empty($chk_project))
                        {
                            $this->{$plugin_class}->set_project($chk_project, FALSE);
                        }

                        // Run plugin
                        if (method_exists($this->{$plugin_class}, $plugin_function))
                        {
    					    $this->print_line($this->{$plugin_class}->$plugin_function());    			     
					    }
					    else
					    {
						    	echo "\nCommand not recognised under plugin '".$plugin_class."'\n";
					    }
                    }
    			     	
				}
				else
				{
					// Try and look for a default command
				    switch ($directive['command']) 
				    {
				    	case "help":
				    	case "?":
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

						/*************************
    				     * Basic plugin commands *
    				     *************************/
						case "shell plugins":
							$this->print_line(print_r($this->plugins, TRUE));  // Print version of OS module
							break;			
						case "shell tree":
							$this->print_line(print_r($this->command_tree, TRUE));  // Print version of OS module
							break;			

						/******************
    				     * Basic commands *
    				     ******************/
						case "shell os":
							$this->print_line($this->os_version);  // Print version of OS module
							break;						
						case "shell project":
							$this->set_project("", TRUE);
							break;
						case "version":
							$this->print_line($this->shell_version);	// Print BURDShell version
							break;					
				
						//Shell debug commands
						case "shell debugon":
							$this->print_line("Debug mode to see directives and parameters only.");
							$this->print_line("Type 'shell debugoff' to finish debug session.");
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
		    $project = $this->get_project();
		    
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
	    $project = $this->get_project();
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
	 * Check to see if shell commands is safe to run.  
	 * If errors found then shell dies to prevent command from ruunning.
	 *
	 *@access	public	
	 *@return	void
	*/
    public function is_safe_env() 
    {
		if ($this->errors_found) {
			echo "\n[NOTICE]Cannot run any commands until shell errors fixed.\n";
			die;
		}    
    }

    private function c_print($colour, $message)
    {
//!TODO: Create colored shell lines    

/*

Black 0;30
Blue 0;34
Green 0;32
Cyan 0;36
Red 0;31
Purple 0;35
Brown 0;33
Light Gray 0;37 
Dark Gray 1;30
Light Blue 1;34
Light Green 1;32
Light Cyan 1;36
Light Red 1;31
Light Purple 1;35
Yellow 1;33
White 1;37

Bold Background
\e[1;30m \e[40m # black
\e[1;31m \e[41m # red
\e[1;32m \e[42m # green
\e[1;33m \e[43m # yellow
\e[1;34m \e[44m # blue
\e[1;35m \e[45m # purple
\e[1;36m \e[46m # cyan
\e[1;37m \e[47m # white


    echo "\033[31m some colored text \033[0m some white text \n";
    echo "\033[32m some colored text \033[0m some white text \n";
    
    
    NOTE: to close the color, you'll have to add \033[0m at the end of the colored output.
*/
    
    }
}
