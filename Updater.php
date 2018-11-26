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

	//	Processed
	private $counts = array(
		'css' => 0,
		'js' => 0,
		'php' => 0
	);

	//	Choices
	private $choices = array();

	//	Envs
	private $envs = array();

	//	Parameters
	private $environments = array(
		'PROD' => array(
			'user' => 'www-data',
			'type' => 'deploy',						// Type of update: [deploy: use master.zip | site: copy source from site]
			'siteFolder' => 'producao',
			'backupFolder' => 'backup',
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
				//'/etc/init.d/network status',
			),
			'posCommands' => array(
				//'sudo /etc/init.d/apache2 start',
			),
			'process' => array(
				'css' => array(
					'enable' => true,
					'source' => array(
						'/assets'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
					),
				), 
				'js' => array(
					'enable' => false,
					'source' => array(
						'/assets/**/*'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
					),
				),
				'php' => array(
					'enable' => false,
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
		
		$this->print('', true, 100, true, 'b0');
		$this->print('', true, 0);
		
		$this->qpre = 1;
		$this->print('Updater Console PHP from Git Sources', true, -1, true, 'b0');
		$this->print('Version: '. $this->version, true, -1, true, 'b0');

		$this->print('', true, 0);
		$this->print('', true, 100, true, 'b0');
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
		$this->print('', true, 0);

		$method = $this->environments[$this->choices['env']]['type'];
		$this->$method();
	}

	/**	
	 * 	Update from deploy
	 **/
	private function deploy() {
		
		$this->print('Archive for deploy -', true, 1, true, 'bb');
		$this->prompt('', array(), 'file', false);
		//	Folder for sources
		$this->choices['folder'] = substr($this->choices['file'], 0, -4);
		
		$this->print('', true, 0);

		//	Summary
		$this->print('Update Summary -', true, 1, true, 'bb');
		$this->print('Site folder: ', true, -1, true, 'b0');
		$this->print('['. $this->environments[$this->choices['env']]['siteFolder'] .']', true, -4);
		$this->print('', true, 0);

		$this->print('Files that will be kept: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['persistentFiles']) .']', true, -4);
		$this->print('', true, 0);

		$this->print('Folders that will be kept: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['persistentFolders']) .']', true, -4);
		$this->print('', true, 0);
		
		$this->print('Folders to be created: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['createIfNotExistsFolders']) .']', true, -4);
		$this->print('', true, 0);
		
		$this->print('Pre-upgrade commands: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['preCommands']) .']', true, -4);
		$this->print('', true, 0);
		
		$this->print('Pos-upgrade commands: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['posCommands']) .']', true, -4);
		$this->print('', true, 0);
		
		$this->print('Files to be processed: ', true, -1, true, 'b0');
		if ($this->environments[$this->choices['env']]['process']['css']['enable']) {
			$this->print("CSS", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['css']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['css']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['css']['ignoreFolders']) .']', true, -7);
		}
		
		if ($this->environments[$this->choices['env']]['process']['js']['enable']) {
			$this->print("JS", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['js']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['js']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['js']['ignoreFolders']) .']', true, -7);
		}

		if ($this->environments[$this->choices['env']]['process']['html']['enable']) {
			$this->print("HTML", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['html']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['html']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['html']['ignoreFolders']) .']', true, -7);
		}

		if ($this->environments[$this->choices['env']]['process']['php']['enable']) {
			$this->print("PHP", true, -4);
			$this->print("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['php']['source']) .']', true, -7);
			$this->print("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['php']['ignoreFiles']) .']', true, -7);
			$this->print("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['php']['ignoreFolders']) .']', true, -7);
		}
		
		$this->print('', true, 0);

		$this->print('Do you want to proceed with the update? -', true, 1, true, 'bb');
		$this->prompt('[Y/N] : ', array('Y','N'), 'confirm');
		
		$this->print('', true, 0);

		if ($this->choices['confirm'] != 'Y'){
			$this->abort('Upgrade canceled by user');
		}
		
		$this->print('', true, 0);

		$this->print('Starting update process ...', true, 0, true, 'bg');

		//	Verifing files/folders
		$this->deployCheckFiles();
		
		//	Backup
		//$this->backup();

		//	Pre-commands
		$this->preCommands();

		//	Unzip Files
		$this->print('Decompressing files -', true, 1, true, 'bb');
		$this->print($this->path . $this->choices['file'], true, -1, true, 'b0');
		$this->unzip($this->path . $this->choices['file']);
		$this->print('', true, 0);
		
		//	Process files
		$this->deployProcessFiles();
	}

	public function deployProcessFiles() {
		
		$this->print('Processing files -', true, 1, true, 'bb');
		$this->print('Compressing: ', true, -1, true, 'b0');
		
		foreach ($this->environments[$this->choices['env']]['process'] as $k => $v) {
			if ($v['enable']) {
				$this->print(strtoupper($k), true, -4);
				foreach ($v['source'] as $kk => $vv) {
					$this->print($vv, true, -6);
					//$this->folder($this->path . $this->environments[$this->choices['env']]['sourceFolder'] . $vv, 'teste', $k);
				}
			}
		}

	}

	/**	
	 * 	Verify files/folders
	 **/
	public function deployCheckFiles() {

		$this->qpre = 4;

		$this->print('Checking files/folders important for updating -', true, 1, true, 'bb');

		$this->print('Folder(s) site: ', true, -1, true, 'b0');
		$this->pathCheck($this->environments[$this->choices['env']]['siteFolder']);
		$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . '_old');
		$this->print('', true, 0);
		
		$this->print('Persistent file(s): ', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFiles'] as $k => $v) {
			$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . $v);
		}
		$this->print('', true, 0);
		
		$this->print('Persistent folder(s): ', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFolders'] as $k => $v) {
			$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . $v);
		}
		$this->print('', true, 0);

		$this->print('File to deploy: ', true, -1, true, 'b0');
		$this->pathCheck($this->choices['file']);
		$this->print('', true, 0);
	}

	/**	
	 * 	Pre-Commands
	 **/
	public function preCommands() {

		$this->print('Running pre-commands -', true, 1, true, 'bb');
		foreach ($this->environments[$this->choices['env']]['preCommands'] as $k => $v) {
			$this->print($v, true, -1, true, 'b0');
			$this->print($this->execute($v), true, 0);
		}
		$this->print('', true, 0);
	}

	/**	
	 * 	Backup
	 **/
	public function backup() {

		$date = date('dmY_His');

		$this->print('Backing up the "'. $this->choices['env'] .'" site -', true, 1, true, 'bb');

		$sourceFolder = $this->environments[$this->choices['env']]['siteFolder'];
		$destinyFolder = $this->environments[$this->choices['env']]['backupFolder'] .'/'. $this->environments[$this->choices['env']]['siteFolder'] .'_'. $date;

		$this->print('Checking... ', true, -1, true, 'b0');
		
		if (!$this->folderCreate($destinyFolder)) {
			$this->abort('Could not create backup: '. $this->path . $destinyFolder);
		}
		$this->print('', true, 0);

		$this->print('Copying... ', true, -1, true, 'b0');
		$this->print('Source: '. $this->path . $sourceFolder, true, -4);
		$this->print('Destiny: '. $this->path . $destinyFolder, true, -4);
		
		$this->execute('cp -rf "'. $this->path . $sourceFolder .'" "'. $this->path . $destinyFolder .'"');

		if (!$this->pathCheck($destinyFolder)) {
			$this->abort('Could not create backup: '. $this->path . $destinyFolder);
		}
		
		$this->print('Backup completed successfully', true, -4, true, 'ng');
		$this->print('', true, 0);
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
	 * 	Unzip
	 **/
	public function unzip($path) {
		$this->execute('unzip -o "'. $path .'" > /dev/null 2>&1');
	}

	/**	
	 * 	Check path
	 **/
	private function pathCheck($path, $info = true, $label = false, $die = true) {
		
		$pathFull = $this->path . $path;

		($info) ? $this->print("Checking path: '$pathFull' ", true, ($this->qpre * -1)) : null;
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
				return false;
			}
		}
		return true;
	}

	/**	
	 * 	Navigate into folders
	 **/
	public function folder($path, $func, $extension){
		
		$dir = new DirectoryIterator($path);
		
		foreach ($dir as $file) {
		    if (!$file->isDot()) {
		    	if ($file->isDir()) {
		            $path = $file->getPathname();
		            foreach ($this->fignore as $k => $v) {
			            if (strpos($path, $v)) {
		            		continue(2);
		            	}
		            }
		            $this->print('Folder: '. $path, true, -1);
	                $this->folder($path, $func, $extension);
		        }
		        //	Is a file?
		        if ( $file->isFile()) {
		            $filePath = $file->getPathname();
		            $fileName = $file->getFilename();
		            // Is a extension required ?
		            if (preg_match("/\.$extension/", $fileName) && !in_array($fileName, $this->ignore)) {
			            //$o->message($o->lbltab . ' | > ' . $filePath, true, true);
			            file_put_contents($filePath, $this->$func(file_get_contents($filePath), $fileName, $filePath, $extension));
			            $this->counts[$extension]++;
		            }
		        }
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

		echo $this->execute('clear');
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
		return shell_exec("$command");
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
		$this->print('', true, 100, true, 'br'); 
		$this->print('', true, 0);
		$this->print($msg .' ---', true, 3, true, 'br');
		$this->print('', true, 0);
		$this->print('', true, 100, true, 'br'); 
		exit();
	}
}

$updater = new Updater();
$updater->start();