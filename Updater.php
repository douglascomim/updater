<?php
/**	
 * 	System updater manager
 *	@author Douglas Comim <douglas.comim@gmail.com>
 *	@version 2.0 | 19.11.2018
 **/ 
class Updater {

	//	Path execution
	private $path = '/home/douglas/Apps/updater-test/'; // __DIR__
	
	//	Breakline chars
	private $breakLine = "\n";

	//	Prefix
	private $pre = '>';

	//	Version
	private $version = '2.0';

	//	Colors
	private $colors = array(
		'red' => "\033[1;31m",
		'green' => "\033[1;32m",
		'none' => "\033[0m",
	);

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
		$this->print('', 100); 

		$this->print('', 0);
		$this->pathCheck('weblaudos-master.zip');
		$this->pathCheck('teste');
	}

	/**	
	 * 	Check path
	 *	@param String $message
	 **/
	private function pathCheck($path, $info=true, $label=false, $die=true) {
		
		$pathFull = $this->path . $path;

		($info) ? $this->print("Verifing path: '$pathFull' - ", 2) : null;
		if (!file_exists($pathFull)) {
			($info) ? $this->print('[ERROR] [Path not found]', 0, false, 'red') : null;
			($label) ? $this->print($label) : null;
			($die) ? $this->abort() : null;
			return false;
		}
		($info) ? $this->print('[OK]', 0, false, 'green') : null;
		return true;
	}

	/**	
	 * 	Print to screen
	 *	@param String $message
	 **/
	private function print($message, $pre = 1, $brk = true, $color = null) {
		if ($brk) {
			echo $this->breakLine;
			echo str_repeat($this->pre, $pre) . ' ';
		}
		if ($color && isset($this->colors[$color])) {
			$message = $this->colors[$color] . $message . $this->colors['none'];
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
	private function abort($message = 'Execution aborted...') {
		$this->print('', 0);
		$this->print($message, 1, true, 'red');
		$this->print('', 0);
		$this->print('', 100); 
		exit();
	}	
}

$updater = new Updater();
$updater->start();