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
	private $breakLine = "\n\r";

	//	Prefix
	private $pre = '-';

	//	QPre
	private $qpre = 1;

	//	Log
	private $log = array('clean' => '', 'full' => '');

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
		'HOMOLOGATION' => array(),
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
		$this->print('', true , 0);
		
		$this->qpre = 1;
		$this->print('Updater Console PHP from Git');
		$this->print('Version: '. $this->version, true, 2);

		$this->print('', true , 0);
		$this->print('', true, 100);
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
		
		$this->print('Gathering Information for Upgrade', true, 0, true, 'bg');
		
		$this->print('Which environment do you want to upgrade -', true, 1, true, 'bb');
		$this->prompt('['. implode(' / ', $this->envs) .'] : ', $this->envs, 'env');
		$this->print('', true , 0);

		$method = $this->environments[$this->choices['env']]['type'];
		$this->$method();
	}

	/**	
	 * 	Update from deploy
	 **/
	private function deploy() {
		
		$this->print('Archive for deploy -', true, 1, true, 'bb');
		$this->prompt('', array(), 'file', false);
		
		$this->print('', true , 0);

		//	Summary
		$this->print('Update Summary -', true, 1, true, 'bb');
		$this->print('Site folder: ', true, -1, true, 'b0');
		$this->print('['. $this->environments[$this->choices['env']]['siteFolder'] .']', true, -4);
		$this->print('', true , 0);

		$this->print('Files that will be kept: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['persistentFiles']) .']', true, -4);
		$this->print('', true , 0);

		$this->print('Folders that will be kept: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['persistentFolders']) .']', true, -4);
		$this->print('', true , 0);
		
		$this->print('Folders to be created: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['createIfNotExistsFolders']) .']', true, -4);
		$this->print('', true , 0);
		
		$this->print('Pre-upgrade commands: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['preCommands']) .']', true, -4);
		$this->print('', true , 0);
		
		$this->print('Pos-upgrade commands: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['posCommands']) .']', true, -4);
		$this->print('', true , 0);
		
		$this->print('Files to be compressed: ', true, -1, true, 'b0');
		if ($this->environments[$this->choices['env']]['compress']['css']['enable']) {
			$this->print("CSS", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['compress']['css']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['compress']['css']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['compress']['css']['ignoreFolders']) .']', true, -7);
		}
		
		if ($this->environments[$this->choices['env']]['compress']['js']['enable']) {
			$this->print("JS", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['compress']['js']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['compress']['js']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['compress']['js']['ignoreFolders']) .']', true, -7);
		}

		if ($this->environments[$this->choices['env']]['compress']['html']['enable']) {
			$this->print("HTML", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['compress']['html']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['compress']['html']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['compress']['html']['ignoreFolders']) .']', true, -7);
		}

		if ($this->environments[$this->choices['env']]['compress']['php']['enable']) {
			$this->print("PHP", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['compress']['php']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['compress']['php']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['compress']['php']['ignoreFolders']) .']', true, -7);
		}
		
		$this->print('', true , 0);

		$this->print('Do you want to proceed with the update ? -', true, 1, true, 'bb');
		$this->prompt('[S/N] : ', array('S','N'), 'confirm');
		
		$this->print('', true , 0);

		if ($this->choices['confirm'] != 'S'){
			$this->abort('Upgrade canceled by user');
		}
		
		$this->print('', true , 0);

		$this->print('Starting update process ...', true, 0, true, 'bg');

		//	Verifing files/folders
		$this->deployCheckFiles();
	}

	/**	
	 * 	Verify files/folders
	 **/
	public function deployCheckFiles() {

		$this->qpre = 4;

		$this->print('Checking files/folders important for updating...', true, 1, true, 'bb');

		$this->print('Folder(s) site: ', true, -1, true, 'b0');
		$this->pathCheck($this->environments[$this->choices['env']]['siteFolder']);
		$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . '_old');
		$this->print('', true , 0);
		
		$this->print('Persistent file(s): ', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFiles'] as $k => $v) {
			$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . $v);
		}
		$this->print('', true , 0);
		
		$this->print('Persistent folder(s): ', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFolders'] as $k => $v) {
			$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . $v);
		}
		$this->print('', true , 0);

		$this->print('File to deploy: ', true, -1, true, 'b0');
		$this->pathCheck($this->choices['file']);
		$this->print('', true , 0);
	}

	/**	
	 * 	Prompt
	 **/
	public function prompt($msg, $valid = array(), $key, $test = true) {

		$this->print('', true, -1, false, false);
		
		$prompt = readline($msg);
		
		if ($prompt != '') {
			if (!$test) {
				$this->print($msg ."[$prompt]", true, -1);
				$this->choices[$key] = $prompt;
				return true;
			} else {
				if (in_array($prompt, $valid)){
					$this->print($msg ."[$prompt]", true, -1);
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

		($info) ? $this->print("Verifing path: '$pathFull' ", true, ($this->qpre * -1)) : null;
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
	 * 	Create Folder
	 **/
	private function folderCreate($path, $mask = 0755) {
		if (!$this->pathCheck($path, true, false, false)) {
			if (!mkdir($this->path . $path, $mask, true)) {
				$this->abort('Could not create folder');
			}
		}
	}

	/**	
	 * 	Print to screen
	 **/
	private function print($msg, $brk = true, $pre = null, $save = true, $color = null) {

		if ($brk) {
			$pre = ($pre === null) ? $this->qpre : $pre;
			if ($pre == 0) {
				$msg = $this->breakLine . str_repeat($this->pre, $pre) . $msg;
			} else if ($pre >= 0) {
				$msg = $this->breakLine . str_repeat($this->pre, $pre) .' '. $msg;
			} else {
				$msg = $this->breakLine . str_repeat(' ', $pre * -1) .' '. $msg;
			}
		}

		$msgc = $msg;

		if ($color && isset($this->colors[$color])) {
			$msgc = $msg; // message clean
			$msg = $this->colors[$color] . $msg . $this->colors['none'];
		}

		$this->execute('clear');
		echo $this->log['full'] . $msg;

		if ($save) {
			$this->log['clean'] .= $msgc;
			$this->log['full'] .= $msg;
		}
	}

	/**	
	 * 	Execute commands
	 **/
	private function execute($command) {
		echo shell_exec("$command");
	}

	/**	
	 * 	Log
	 **/
	private function log() {
		file_put_contents('update_'. date('dmYHi') .'.log', $this->log['clean']);
	}

	/**	
	 * 	Clear screen
	 **/
	private function clear() {
		$this->execute('clear');
	}

	/**	
	 * 	Abort
	 **/
	private function abort($msg = 'Execution aborted') {
		$this->print('', true, 100); 
		$this->print('', true, 0);
		$this->print($msg .' ---', true, 3, true, 'br');
		$this->print('', true, 0);
		$this->print('', true, 100); 
		exit();
	}
}

$updater = new Updater();
$updater->start();