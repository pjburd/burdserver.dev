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

abstract class BURDShell_Plugin extends BURDShell_interface
{    
	/*
	 * @var	array	List of commands function aliasing e.g. array ('list', 'help' => 'shelp');
	 */
    public $commands = array();

    
    /*
     * Help for this command
     *
	 * @return	object
     */   
    abstract public function help();
    
    /*
     * Returns a list of known commands for this function
     *
	 * @return	object
     */        
    public function get_commands() 
    {
        return $this->commands;       
    }
    
}