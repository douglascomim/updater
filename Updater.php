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

	//	QPre
	private $qpre = 1;

	//	Log
	private $log = '';

	//	Version
	private $version = '2.0';

	//	Colors
	private $colors = array(
		'red' => "\033[1;31m",
		'green' => "\033[1;32m",
		'none' => "\033[0m",
	);

	//	Envs
	private $envs = array();

	//	Parameters
	private $environments = array(
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

		foreach ($this->environments as $k => $v) {
			$this->envs[] = $k;
		}

		$this->header();
		$this->body();
	}

	/**	
	 * 	Header
	 **/
	private function header() {
		
		$this->print('', true, 100);
		$this->print('', true , -1);
		
		$this->qpre = 1;
		$this->print('Updater Console PHP');
		$this->print('Version: ' . $this->version);

		$this->print('', true , -1);
		$this->print('', true, 100);
		$this->print('', true , -1);
	}

	/**	
	 * 	Body
	 *  -- Get info about enviroment to will been updated
	 *  -- Check files to update
	 *  -- Check enviroment exists
	 *  -- Check enviroment exists
	 **/
	private function body() {
		$this->qpre = 2; 
		
		$this->print('Which environment do you want to upgrade ?', true, 1);
		$this->prompt('['. implode('|', $this->envs) .'] : ', $this->envs);
	}

	/**	
	 * 	Prompt
	 **/
	public function prompt($msg, $valid = array(), $test = true) {

		$this->print('', true, null, false, false);
		
		$prompt = '';
		$prompt = readline($msg);
		
		if (!$test) {
			$this->print("$msg [$prompt]");
			return $prompt;	
		} else {
			if (in_array($prompt, $valid)){
				$this->print("$msg [$prompt]");
				return $prompt;
			}
		}
		return $this->prompt($msg, $valid);
	}


	/**	
	 * 	Check path
	 *	@param String $message
	 **/
	private function pathCheck($path, $info = true, $label = false, $die = true) {
		
		$pathFull = $this->path . $path;

		($info) ? $this->print("Verifing path: '$pathFull' - ", 2) : null;
		if (!file_exists($pathFull)) {
			($info) ? $this->print('[ERROR] [Path not found]', 0, false, null, true, 'red') : null;
			($label) ? $this->print($label) : null;
			($die) ? $this->abort() : null;
			return false;
		}
		($info) ? $this->print('[OK]', 0, false, null, true, 'green') : null;
		return true;
	}

	/**	
	 * 	Print to screen
	 *	@param String $message
	 **/
	private function print($msg, $brk = true, $pre = null, $save = true, $color = null) {
		if ($brk) {
			$msg = $this->breakLine . str_repeat($this->pre, ceil($pre ?: $this->qpre)) .' '. $msg;
		}
		if ($color && isset($this->colors[$color])) {
			$msg = $this->colors[$color] . $msg . $this->colors['none'];
		}
		$this->execute('clear');
		echo $this->log . $msg;

		if ($save) {
			$this->log .= $msg;
		}

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