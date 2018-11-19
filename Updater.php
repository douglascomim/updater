<?php
/**	
 * 	System updater manager
 *	@author Douglas Comim <douglas.comim@gmail.com>
 *	@version 2.0 | 19.11.2018
 **/ 
class Updater {

	//	Path execution
	private $path = '/home/douglas/Apps/updater-test'; // __DIR__
	
	//	Breakline chars
	private $breakLine = "\n";

	//	Prefix
	private $pre = ">";

	//	Version
	private $version = '2.0';

	//	Parameters
	private $enviroments = array(
		'PRODUCTION' => array(
			'user' => 'www-data',
			'update' => 'deploy',						// Type of update: [deploy: use master.zip | site: copy source from site]
			'siteFolder' => 'producao',
			'sourceFolder' => 'weblaudos-master',
			'persistentFiles' => array(
				'/application/config/config.php',
				'/application/config/constants.php',
				'/application/config/database.php',
			),
			'persistentFolders' => array(
				'/files'
			),
			'createIfNotExistsFolders' => array(
				'/temp/trash',
				'/temp/reports'
			),
			'preCommands' => array(
				'sudo /etc/init.d/apache2 stop',
			),
			'posCommands' => array(
				'sudo /etc/init.d/apache2 start',
			),
			'compress' => array(
				'css' => array(
					'enable' => true,
					'source' => array(
						'/assets/**/*'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
					),
				), 
				'js' => array(
					'enable' => true,
					'source' => array(
						'/assets/**/*'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
					),
				),
				'html' => array(
					'enable' => true,
					'source' => array(
						'/assets/**/*'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
					),
				),
				'php' => array(
					'enable' => true,
					'source' => array(
						'/assets/**/*'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
					),
				), 
			),
		),
	);

	/**	
	 * 	Main method
	 **/
	public function start() {
		$this->execute('clear');
		$this->header();
	}

	/**	
	 * 	Header
	 **/
	private function header() {
		
		$this->print('', 100); 
		$this->print('', 0);

		$this->print('Updater Console');
		$this->print('Version: ' . $this->version);

		$this->print('', 0);
		$this->pathCheck('teste');
	}

	/**	
	 * 	Check path
	 *	@param String $message
	 **/
	private function pathCheck($path, $info=true, $label=false, $die=true) {
		
		($info) ? $this->print("Verifing: $path", 2) : null;
		if (!file_exists($path)) {
			($info) ? $this->print(' - [ERROR] [Path not found]', 3, false) : null;
			($label) ? $this->print($label) : null;
			($die) ? $this->abort() : null;
			return false;
		}
		($info) ? $this->print(' - [ OK ]') : null;
		return true;
	}

	/**	
	 * 	Print to screen
	 *	@param String $message
	 **/
	private function print($message, $pre = 1, $brk = true) {
		if ($brk) {
			echo $this->breakLine;
			echo str_repeat($this->pre, $pre) . ' ';
		}
		echo $message;
	}

	/**	
	 * 	Execute commands
	 *	@param String $command
	 **/
	private function execute($command) {
		echo shell_exec("$command");
	}

	/**	
	 * 	Abort
	 **/
	private function abort($message = 'Aborting update...') {
		$this->print($message);
		exit();
	}	
}

$updater = new Updater();
$updater->start();