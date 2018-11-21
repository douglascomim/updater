<?php
/**	
 * 	System updater manager
 *	@author Douglas Comim Pinheiro <douglas.comim@gmail.com>
 *	@version 2.0 | 19.11.2018
 **/ 
class Updater {

	//	Path execution
	private $path = '/home/douglas/Apps/updater-test/'; // __DIR__
	
	//	Breakline chars
	private $breakLine = "\n";

	//	Prefix
	private $pre = '-';

	//	QPre
	private $qpre = 1;

	//	Log
	private $log = '';

	//	Version
	private $version = '2.0';

	//	Colors
	private $colors = array(
		
		//	Normal
		'n' => "\033[0;30m", 				// black
		'nr' => "\033[0;31m", 				// red
		'ng' => "\033[0;32m",				// green
		'nb' => "\033[0;34m",				// blue

		//	underline
		'u0' => "\033[4m", 					
		'u' => "\033[4;30m", 				// black
		'ur' => "\033[4;31m", 				// red
		'ug' => "\033[4;32m",				// green
		'ub' => "\033[4;34m",				// blue

		//	Bold
		'b0' => "\033[1m", 				
		'b' => "\033[1;30m", 				// black
		'br' => "\033[1;31m", 				// red
		'bg' => "\033[1;32m",				// green
		'bb' => "\033[1;34m",				// blue

		'none' => "\033[0m",
	);

	//	Choices
	private $choices = array();

	//	Envs
	private $envs = array();

	//	Parameters
	private $environments = array(
		'PRODUCTION' => array(
			'user' => 'www-data',
			'type' => 'deploy',						// Type of update: [deploy: use master.zip | site: copy source from site]
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
		
		$this->log();
	}

	/**	
	 * 	Header
	 **/
	private function header() {
		
		$this->print('', true, 100);
		$this->print('', true , -1);
		
		$this->qpre = 1;
		$this->print('Updater Console PHP from Git');
		$this->print('Version: '. $this->version, true, 2);

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
		
		$this->print('Which environment do you want to upgrade', true, 1, true, 'bb');
		$this->prompt(implode('|', $this->envs) .': ', $this->envs, 'env');

		$method = $this->environments[$this->choices['env']]['type'];
		$this->$method();
	}

	/**	
	 * 	Update from deploy
	 **/
	private function deploy() {
		
		$this->print('Archive for deploy', true, 1, true, 'bb');
		$this->prompt('', array(), 'file', false);
		
		$this->print('', true , -1);

		//	Summary
		$this->print('Update Summary -', true, 1, true, 'bb');
		$this->print('Site folder: ', true, 2, true, 'b0');
		$this->print("\t[". $this->environments[$this->choices['env']]['siteFolder'] .']', true, -1);
		
		$this->print('Files that will be kept: ', true, 2, true, 'b0');
		$this->print("\t[". implode(', ', $this->environments[$this->choices['env']]['persistentFiles']) .']', true, -1);

		$this->print('Folders that will be kept: ', true, 2, true, 'b0');
		$this->print("\t[". implode(', ', $this->environments[$this->choices['env']]['persistentFolders']) .']', true, -1);

		$this->print('Folders to be created: ', true, 2, true, 'b0');
		$this->print("\t[". implode(', ', $this->environments[$this->choices['env']]['createIfNotExistsFolders']) .']', true, -1);

		$this->print('Pre-upgrade commands: ', true, 2, true, 'b0');
		$this->print("\t[". implode(', ', $this->environments[$this->choices['env']]['preCommands']) .']', true, -1);

		$this->print('Pos-upgrade commands: ', true, 2, true, 'b0');
		$this->print("\t[". implode(', ', $this->environments[$this->choices['env']]['posCommands']) .']', true, -1);
		
	}


	/**	
	 * 	Prompt
	 **/
	public function prompt($msg, $valid = array(), $key, $test = true) {

		$this->print('', true, null, false, false);
		
		$prompt = readline($msg);
		
		if ($prompt != '') {
			if (!$test) {
				$this->print($msg ."[$prompt]");
				$this->choices[$key] = $prompt;
				return true;
			} else {
				if (in_array($prompt, $valid)){
					$this->print($msg ."[$prompt]");
					$this->choices[$key] = $prompt;
					return true;
				}
			}
		}
		$this->prompt($msg, $valid, $key, $test);
	}


	/**	
	 * 	Check path
	 **/
	private function pathCheck($path, $info = true, $label = false, $die = true) {
		
		$pathFull = $this->path . $path;

		($info) ? $this->print("Verifing path: '$pathFull' - ", 2) : null;
		if (!file_exists($pathFull)) {
			($info) ? $this->print('[ERROR] [Path not found]', false, null, true, 'br') : null;
			($label) ? $this->print($label) : null;
			($die) ? $this->abort() : null;
			return false;
		}
		($info) ? $this->print('[OK]', false, null, true, 'bg') : null;
		return true;
	}

	/**	
	 * 	Print to screen
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
	 **/
	private function execute($command) {
		echo shell_exec("$command");
	}

	/**	
	 * 	log
	 **/
	private function log() {
		file_put_contents('update_'. date('dmYHi') .'.log', $this->log);
	}

	/**	
	 * 	Abort
	 **/
	private function abort($msg = 'Execution aborted...') {
		$this->print('', true, -1);
		$this->print($msg, true, 1, true, 'br');
		$this->print($msg, false, null, true, 'bg');
		$this->print('', true, -1);
		$this->print('', true, 100); 
		exit();
	}
}

$updater = new Updater();
$updater->start();