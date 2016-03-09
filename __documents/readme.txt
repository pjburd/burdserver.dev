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
==========
README.TXT
==========


Contents

1 About
2 Usage
3 Installation
3.1 Software requirements
3.2 Important safety thoughts!
3.3 Simple installation (Quick)
3.4 Server installation (Long)
3.5 Dedicated server installation (Complex)
4 Support
4.1 Server support


1 About
=======
Thank you for choosing 'BURDShell' to manage your development.

BURDShell is a developer platform shell extensible plugins to help manage your private projects in-house before going public via a command line.

Once the platform shell is setup you can easily creating websites, projects, svn repos, and even backups via a command line.

2 Usage
=======
See tutorial.txt for a tour of the system

Shell works in Ubuntu and Mac
	
	shell.php 					: Runs the BURDShell without a project selected.  All commands will ask for your desired project.
	
	shell.php [project name]    : Sets up your project environment so all commands will not ask which desired project.


For full shell server feature support (Some features are restricted as root is required)

	1) Run the following to become 'root' user
		sudo su -
	2) Then run the shell command
		shell.php


3 Installation
==============
There are two types of installation, dedicated server mirroring within a virtual machine(hard to setup), and native (easier to setup) 
		
3.1 Software requirements
-------------------------
This is the essential software for a fully working BURDShell.

PHP 		 - 5.3.10+
MySQL Server - 5.5.34+
SVN			 - 1.6.17+
Apache       - 2.2.22+		
svn          - 1.7.20+
rlwrap       - 0.42 (For OSx commad history emulation in BURDShell) 
	
The shell can function in the following OS environments:

	Virtually on Ubuntu Server 12.04.3 LTS or later
	Natively on Ubuntu Desktop 12.04 LTS or later
	Natively on Mac OSx 10.9.2 or later

3.2 Important safety thoughts!
------------------------------
Installing in a virtual machine enviromnemt for your own additional safety.

Switching the network to private 'host-only' for additional safety of your projects you work on to begin with.

Make sure you change IP address, passwords, certificates and usernames as you see fit.

[Optional security]
Make sure you harden your server before you switching network to public or private phsyical networking in a shared office.

		
3.3 Simple installation (Quick)
------------------------------
The following files are provided to help with quick installation

	native-install-ubuntu.txt : Quick installation of BURDShell on Ubuntu.
	native-install-osx.txt    : Quick installation of BURDShell on OSx.
	
3.4 Server installation (Long)
------------------------------
These are steps to setup a dedicate environment.  Ideal for dedicated mirroring of project system.

	support/server/server-install-steps.txt : All the main steps to creating the perfect shell environment. 
          									  This will set up FTP, SSL, SSH, Apache, MySQL, PHP
        									  There are optional extras (some not fully tested, you have been warned)
	
	shell-installation.txt		            : Follow 'SHELL INSTALLATION' steps in 'shell-readme.txt'
	
	support/server/server-user-guide.txt    : Managing the server guide (DRAFT rough guide).

3.5 Dedicated server installation (Complex)
-------------------------------------------

Currently only Ubuntu server 12.04 LTS is only available.

1) Choose desired virtual machine OS software
		e.g. VirtualBox, VMWare, or VirtualPC

2) Follow the 'server-install-steps.txt' steps

		!!!! Take special care with using unique passwords - Change 'Current servers' details !!!!!
		!!!! HARDENING SERVER - Take care to noting down unique PORTS !!!!

3) Follow 'SHELL INSTALLATION' steps in 'shell-readme.txt'

4) Take note of the foolowing
		'virtual-install-[Desired OS].txt
		'switching-networks.txt'


Touch wood, Your envionment should be ready.


4 Support
=========
Documentation can be found in __documents/  folder

More documentation can be helpful.
support/tutorial.txt              : Test tour of all the main features of BURDShell
support/plugin-development.txt    : Expand your BURDShell commands by creating your own plugins.
support/changelog.txt             : Version history of changes
support/enhancement-notes.txt	  : Thoughts and notes about future enhancements
support/known-issues.txt	      : Known issues about security or current defects
support/migration.txt	          : Migration steps between specific BURDShell versions
support/troubleshoot.txt          : Known solvable problems

4.1 Server support
------------------
Below is guides to assist in setting up dedicated servers for BURDShell that maybe shared.

support/server/server-install-steps.txt : Basic Ubuntu dedicated server installation
support/server/server-user-guide.txt    : Server guide (WIP)
support/server/switching-networks.txt   : Virtual machine guide to switching between networks to broadcast BURDShell
support/server/virtual-install-ubuntu.txt : Setup Virtual machine public broadcasting (Expiremental)