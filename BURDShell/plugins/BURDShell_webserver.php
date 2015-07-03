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

class BURDShell_webserver extends BURDShell_Plugin
{
    public $commands = array('status', 
                             'sites',
                             'restart',
                             'test');   // TODO: Need to make alias system for command to right as 'static'

    /*
     * @access public
     *
	 * @return	object
     */        

    public function help() 
    {
    	$out = "";
    	
		$out .= "webserver sites   : Show a list known sites enabled on webserver\n";
		$out .= "webserver status  : Show status of web server\n";
		$out .= "webserver restart : Restart web server\n";
		$out .= "webserver test    : Test webserver config\n";
	
        return $out;            
    }

	public function sites() 
	{
    	switch (Config::$shell_os)
    	{
        	case 'Ubuntu':
        		$this->print_output($this->admin_exec("apache2ctl -S", FALSE));	
        		break;
        	case 'OSx':	
        	    exec("apachectl -S", $out_lines);	    
        		$this->print_output($out_lines);
        		break;
        }
    }
    
	public function status() 
	{
    	switch (Config::$shell_os)
    	{
        	case 'Ubuntu':
        		$this->print_output($this->admin_exec("service apache2 status", FALSE));
        		break;
        	case 'OSx':
        	    exec("ps aux | grep httpd", $out_lines);	    
        		$this->print_output($out_lines);
        		break;	 		
		}
	}
	    
	public function test() 
	{
    	$this->print_line("This may take a while...","i");
        exec("apachectl configtest", $out_lines);	    
    	$this->print_output($out_lines);
	}
	    
	public function restart() 
	{
    	switch (Config::$shell_os)
    	{
        	case 'Ubuntu':
        		$this->print_output($this->admin_exec("service apache2 status", FALSE));
        		break;
        	case 'OSx':
                $this->print_output($this->admin_exec("apachectl restart"));	 			   	 			        		
                break;
        }
        $this->print_line("Webserver restarted.","i");
    }
}