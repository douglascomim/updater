<?php
/**	
 * 	System updater manager
 * 	
 * 	Thanks
 * 		MatthiasMullie - https://github.com/matthiasmullie/minify
 * 
 *	@author Douglas Comim Pinheiro <douglas.comim@gmail.com>
 *	@version 2.1 | 19.11.2018
 **/ 

error_reporting(E_ERROR | E_WARNING | E_PARSE);

include 'vendor/matthiasmullie/minify/src/Minify.php';
include 'vendor/matthiasmullie/minify/src/JS.php';
include 'vendor/matthiasmullie/minify/src/CSS.php';
include 'vendor/matthiasmullie/path-converter/src/ConverterInterface.php';
include 'vendor/matthiasmullie/path-converter/src/Converter.php';
include 'vendor/matthiasmullie/path-converter/src/NoConverter.php';

use MatthiasMullie\Minify;
use MatthiasMullie\PathConverter\Converter;

class Updater {

	//	Path execution
	private $path = '/home/douglas/Apps/updater-test/'; // __DIR__
	
	//	Breakline chars
	private $breakLine = "\n\r";

	//	Prefix
	private $pre = '-';

	//	QPre
	private $qpre = 1;

	//	Return Exec
	private $rexec = array('output','code');

	//	Log
	private $log = array('clean' => '', 'full' => '');

	//	Log full
	private $logfull = true;

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
		'persistent_files' => array(
			'copy' => array(0, 0)
		),
		'persistent_folders' => array(
			'copy' => array(0, 0)
		),
		'create_folders' => array(
			'create' => array(0, 0)
		),
		'remove_folders' => array(
			'remove' => array(0, 0)
		),
		'compress' => array(
			'css' => array(0, 0), // success, error
			'js' => array(0, 0),
			'php' => array(0, 0)
		),
		'syntax' => array(
			'css' => array(0, 0),
			'js' => array(0, 0),
			'php' => array(0, 0)
		),
	);

	//	Choices
	private $choices = array();

	//	Envs
	private $envs = array();

	//	Parameters
	private $environments = array(
		'PROD' => array(
			'user' => 'douglas',
			'type' => 'deploy',						// Type of update: [deploy: use master.zip]
			'maskFolder' => '0755',
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
			'createFolders' => array(
				'/temp/trash',
				'/temp/reports'
			),
			'removeFolders' => array(
				'/_dev',
			),
			'preCommands' => array(
				//'/etc/init.d/apache2 stop',
			),
			'posCommands' => array(
				//'/etc/init.d/apache2 start',
			),
			'process' => array(
				'css' => array(
					'enable' => true,
					'process' => array('compress'),
					'syntax' => array(
						'app' => '', 
						'string' => '',
						'expected' => ''
					),
					'source' => array(
						'/assets/css'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
						'/css/images',
						'/css/font-awesome',
						'/css/fonts',
					),
				), 
				'js' => array(
					'enable' => true,
					'process' => array('compress', 'syntax'),
					'syntax' => array(
						'app' => 'node', 
						'string' => '-c {PATH} 2>&1 | tee --append /tmp/output',
						'expected' => ''
					),
					'source' => array(
						'/assets/js'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
						'/js/plugins',
					),
				),
				'php' => array(
					'enable' => true,
					'process' => array('compress', 'syntax'),
					'syntax' => array(
						'app' => 'php', 
						'string' => '-l {PATH}',
						'expected' => 'No syntax errors detected'
					),
					'source' => array(
						'/application',
						'/system',
					),
					'ignoreFiles' => array(
						'smiley_helper.php'
					),
					'ignoreFolders' => array(
						'/cache',
						'/logs',
					),
				), 
			),
		),
		'HMLG' => array(
			'user' => 'douglas',
			'type' => 'deploy',						// Type of update: [deploy: use master.zip]
			'maskFolder' => '0755',
			'siteFolder' => 'homologacao',
			'backupFolder' => 'backup',
			'persistentFiles' => array(
				'/application/config/config.php',
				'/application/config/constants.php',
				'/application/config/database.php',
			),
			'persistentFolders' => array(
				'/files'
			),
			'createFolders' => array(
				'/temp/trash',
				'/temp/reports'
			),
			'removeFolders' => array(
				'/_dev',
			),
			'preCommands' => array(
				//'/etc/init.d/apache2 stop',
			),
			'posCommands' => array(
				//'/etc/init.d/apache2 start',
			),
			'process' => array(
				'css' => array(
					'enable' => true,
					'process' => array('compress'),
					'syntax' => array(
						'app' => '', 
						'string' => '',
						'expected' => ''
					),
					'source' => array(
						'/assets/css'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
						'/css/images',
						'/css/font-awesome',
						'/css/fonts',
					),
				), 
				'js' => array(
					'enable' => true,
					'process' => array('compress', 'syntax'),
					'syntax' => array(
						'app' => 'node', 
						'string' => '-c {PATH} 2>&1 | tee --append /tmp/output',
						'expected' => ''
					),
					'source' => array(
						'/assets/js'
					),
					'ignoreFiles' => array(
					),
					'ignoreFolders' => array(
						'/js/plugins',
					),
				),
				'php' => array(
					'enable' => true,
					'process' => array('compress', 'syntax'),
					'syntax' => array(
						'app' => 'php', 
						'string' => '-l {PATH}',
						'expected' => 'No syntax errors detected'
					),
					'source' => array(
						'/application',
						'/system',
					),
					'ignoreFiles' => array(
						'smiley_helper.php'
					),
					'ignoreFolders' => array(
						'/cache',
						'/logs',
					),
				), 
			),
		),
	);

	/**	
	 * 	Main method
	 **/
	public function start() {

		echo $this->execute('clear');

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
		
		$this->message('', true, 100, true, 'b0');
		$this->message('', true, 0, true, 'n0');
		
		$this->qpre = 1;
		$this->message('Updater Console PHP from Git Sources', true, -1, true, 'b0');
		$this->message('Version: '. $this->version, true, -1, true, 'b0');

		$this->message('', true, 0, true, 'n0');
		$this->message('', true, 100, true, 'b0');
	}

	/**	
	 * 	Body
	 **/
	private function body() {
		$this->qpre = 2; 
		
		$this->message('', true, 100, true, 'bb');
		$this->message('Gathering Information for Upgrade', true, 0, true, 'bb');
		$this->message('', true, 100, true, 'bb');
		
		$this->message('Which environment do you want to upgrade -', true, 1, true, 'bb');
		$this->prompt('['. implode(' / ', $this->envs) .'] : ', $this->envs, 'env');
		$this->message('', true, 0, true, 'n0');
		
		$method = $this->environments[$this->choices['env']]['type'];
		$this->$method();
		
		//	Finish
		$this->message('', true, 0, true, 'n0');
		$this->message('', true, 100, true, 'bb');
		$this->message('Finished update process', true, -1, true, 'bb');
		$this->message('', true, 100, true, 'bb');
		$this->message('', true, -1);
	}

	/**	
	 * 	Update from deploy
	 **/
	private function deploy() {
		
		$this->message('Archive for deploy -', true, 1, true, 'bb');
		$this->prompt('[File] : ', array(), 'file', false);
		$this->choices['folder'] = substr($this->choices['file'], 0, -4);
		
		//	Summary
		$this->message('', true, 100, true, 'bb');
		$this->message('Update Summary -', true, 1, true, 'bb');
		$this->message('Site folder: ', true, -1, true, 'b0');
		$this->message('['. $this->environments[$this->choices['env']]['siteFolder'] .']', true, -4, true, 'n0');
		$this->message('', true, 0, true, 'n0', true);

		$this->message('Files that will be kept: ', true, -1, true, 'b0', true);
		$this->message('['. implode(', ', $this->environments[$this->choices['env']]['persistentFiles']) .']', true, -4);
		$this->message('', true, 0);

		$this->message('Folders that will be kept: ', true, -1, true, 'b0');
		$this->message('['. implode(', ', $this->environments[$this->choices['env']]['persistentFolders']) .']', true, -4);
		$this->message('', true, 0);
		
		$this->message('Folders to be created: ', true, -1, true, 'b0');
		$this->message('['. implode(', ', $this->environments[$this->choices['env']]['createFolders']) .']', true, -4);
		$this->message('', true, 0);

		$this->message('Folders to be removed: ', true, -1, true, 'b0');
		$this->message('['. implode(', ', $this->environments[$this->choices['env']]['removeFolders']) .']', true, -4);
		$this->message('', true, 0);
		
		$this->message('Pre-upgrade commands: ', true, -1, true, 'b0');
		$this->message('['. implode(', ', $this->environments[$this->choices['env']]['preCommands']) .']', true, -4);
		$this->message('', true, 0);
		
		$this->message('Pos-upgrade commands: ', true, -1, true, 'b0');
		$this->message('['. implode(', ', $this->environments[$this->choices['env']]['posCommands']) .']', true, -4);
		$this->message('', true, 0);
		
		$this->message('Files to be processed: ', true, -1, true, 'b0');
		if ($this->environments[$this->choices['env']]['process']['css']['enable']) {
			$this->message("CSS", true, -4);
			$this->message("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['css']['source']) .']', true, -7);
			$this->message("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['css']['ignoreFiles']) .']', true, -7);
			$this->message("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['css']['ignoreFolders']) .']', true, -7);
		}
		
		if ($this->environments[$this->choices['env']]['process']['js']['enable']) {
			$this->message("JS", true, -4);
			$this->message("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['js']['source']) .']', true, -7);
			$this->message("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['js']['ignoreFiles']) .']', true, -7);
			$this->message("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['js']['ignoreFolders']) .']', true, -7);
		}

		if ($this->environments[$this->choices['env']]['process']['html']['enable']) {
			$this->message("HTML", true, -4);
			$this->message("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['html']['source']) .']', true, -7);
			$this->message("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['html']['ignoreFiles']) .']', true, -7);
			$this->message("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['html']['ignoreFolders']) .']', true, -7);
		}

		if ($this->environments[$this->choices['env']]['process']['php']['enable']) {
			$this->message("PHP", true, -4);
			$this->message("Sources: [". implode(', ', $this->environments[$this->choices['env']]['process']['php']['source']) .']', true, -7);
			$this->message("Ignored files: [". implode(', ', $this->environments[$this->choices['env']]['process']['php']['ignoreFiles']) .']', true, -7);
			$this->message("Ignored folders: [". implode(', ', $this->environments[$this->choices['env']]['process']['php']['ignoreFolders']) .']', true, -7);
		}
		
		$this->message('', true, 100, true, 'bb');

		$this->message('Do you want to proceed with the update? -', true, 1, true, 'bb');
		$this->prompt('[Y/N] : ', array('Y','N'), 'confirm');
		
		if ($this->choices['confirm'] != 'Y'){
			$this->abort('Upgrade canceled by user');
		}
		
		$this->message('', true, 0);

		$this->message('', true, 100, true, 'bb');
		$this->message('Starting update process ...', true, 0, true, 'bb');
		$this->message('', true, 100, true, 'bb');

		//	Verifing files/folders
		$this->deployCheckFiles();
		
		//	Backup
		$this->backup();

		//	Unzip Files
		$this->message('Decompressing source files -', true, 1, true, 'bb');
		$this->message('Unzip:', true, -1, true, 'b0');
		$this->message($this->path . $this->choices['file'] .' -> '. $this->path, true, -4);
		$this->unzip($this->path . $this->choices['file'], $this->path);
		$this->message('', true, 0);
		
		//	Replace persistent files
		$this->replacePersistentFiles();

		//	Replace persistent folders
		$this->replacePersistentFolders();

		//	Create folders
		$this->createFolders();

		//	Remove folders
		$this->removeFolders();

		//	Process files
		$this->deployProcessFiles();

		//	Pre-commands
		$this->preCommands();

		//	Remove old site
		$this->removeSiteOld();
		
		//	Move new sites
		$this->moveSites();
		
		//	Remove files of installation
		$this->removeFiles();

		//	pos-commands
		$this->posCommands();
	}

	/**	
	 * 	Deploy process files/folders
	**/
	private function deployProcessFiles() {
		
		$this->message('Processing files -', true, 1, true, 'bb');
		$this->message('Compressing: ', true, -1, true, 'b0');
		
		$action = 'compress';
		foreach ($this->environments[$this->choices['env']]['process'] as $k => $v) {
			if ($v['enable']) {
				$this->message(strtoupper($k), true, -4, true, 'b0');
				if (!in_array($action, $this->environments[$this->choices['env']]['process'][$k]['process'])) {
					$this->message("Process '$action' not enabled", true, -8, true, 'b0');
					continue(1);
				}
				foreach ($v['source'] as $kk => $vv) {
					$this->message($vv, true, -6, true, 'b0');
					$this->folder($this->path . $this->choices['folder'] . $vv, $action, $k);
				}
			}
		}

		$this->message('', true, 0);
		$this->message('Testing syntax: ', true, -1, true, 'b0');
		
		$action = 'syntax';
		foreach ($this->environments[$this->choices['env']]['process'] as $k => $v) {
			if ($v['enable']) {
				$this->message(strtoupper($k), true, -4, true, 'b0');
				if (!in_array($action, $this->environments[$this->choices['env']]['process'][$k]['process'])) {
					$this->message("Process '$action' not enabled", true, -8, true, 'b0');
					continue(1);
				}
				foreach ($v['source'] as $kk => $vv) {
					$this->message($vv, true, -6, true, 'b0');
					$this->folder($this->path . $this->choices['folder'] . $vv, $action, $k);
				}
			}
		}

		$this->message('', true, 0);
		$this->message('Changing Permissions -', true, 1, true, 'bb');
		$this->message('Folder:', true, -1, true, 'b0');
		$this->message('Permissions: '. $this->environments[$this->choices['env']]['maskFolder'], true, -4, true, 'n0');
		$this->message('Changing: '. $this->path . $this->choices['folder'], true, -4, true, 'n0');
		
		if (!$this->pathCheck($this->choices['folder'], false, false, false)) {
			$this->message(' [FAIL]', false, 0, true, 'nr');
			$this->abort('Folder not found.');
		}
		if ($this->folderPermission($this->path . $this->choices['folder'], $this->environments[$this->choices['env']]['maskFolder'])) {
			$this->message(' [OK]', false, 0, true, 'ng');
		} else {
			$this->message(' [FAIL]', false, 0, true, 'nr');
		}
		
		$this->message('', true, 0);
		$this->message('Changing Owner -', true, 1, true, 'bb');
		$this->message('Folder:', true, -1, true, 'b0');
		$this->message('Owner: '. $this->environments[$this->choices['env']]['user'] .':'. $this->environments[$this->choices['env']]['user'], true, -4, true, 'n0');
		$this->message('Changing: '. $this->path . $this->choices['folder'], true, -4, true, 'n0');
		
		if (!$this->pathCheck($this->choices['folder'], false, false, false)) {
			$this->message(' [FAIL]', false, 0, true, 'nr');
			$this->abort('Folder not found.');
		}
		if ($this->folderOwner($this->path . $this->choices['folder'], $this->environments[$this->choices['env']]['user'], $this->environments[$this->choices['env']]['user'])) {
			$this->message(' [OK]', false, 0, true, 'ng');
		} else {
			$this->message(' [FAIL]', false, 0, true, 'nr');
		}

		$this->message('', true, 0);
		$this->message('', true, 100, true, 'bb');
		$this->message('Summary of processing -', true, 1, true, 'bb');

		foreach ($this->counts as $k => $v) {
			$this->message(strtoupper($k), true, -1, true, 'b0');
			foreach ($this->counts[$k] as $kk => $vv) {
				$this->message(str_pad(strtolower($kk), 25), true, -4, true, 'b0');
				$this->message("\t". $vv[1], false, 0, true, 'br');
				$this->message(' / ', false, 0);
				$this->message($vv[0], false, 0, true, 'bg');
				$this->message(' / ', false, 0);
				$this->message(($vv[0] + $vv[1]), false, 0, true, 'b0');
			}
		}

		$this->message('', true, 100, true, 'bb');
		$this->message('Do you want to continue the update? -', true, 1, true, 'bb');
		$this->prompt('[Y/N] : ', array('Y','N'), 'confirm');
		if ($this->choices['confirm'] != 'Y'){
			$this->abort('Upgrade canceled by user');
		}
		$this->message('', true, 100, true, 'bb');
	}
	
	/**	
	 * 	Remove files
	 **/
	private function removeFiles() {
		
		$this->message('', true, 0);
		$this->message('Removing files(s) -', true, 1, true, 'bb');
		$this->message('File: '. $this->path . $this->choices['file'], true, -1, true, 'b0');
		$this->execute('rm '.$this->path . $this->choices['file']);
		if (!$this->rexec['code']) {
			$this->message(' [FAIL]', false, 0, true, 'nr');
		}
		$this->message(' [OK]', false, 0, true, 'ng');
		$this->message('', true, 0);
	}

	/**	
	 * 	Remove Old site
	 **/
	private function removeSiteOld() {

		$this->message('', true, 0);
		$this->message('Removing Old Site(s) -', true, 1, true, 'bb');
		$this->message('Site: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old', true, -1, true, 'b0');
		if ($this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . '_old', false, false, false)) {
			if (!$this->folderRemove($this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old')) {
				$this->abort('Failed to delete old site');
			}
		}
		$this->message(' [OK]', false, 0, true, 'ng');
	}

	/**	
	 * 	Move sites
	 **/
	private function moveSites() {

		$this->message('', true, 0);
		$this->message('Moving Site(s) -', true, 1, true, 'bb');
		$this->message('Site: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] .' '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old', true, -1, true, 'b0');
		if (!$this->folderMove($this->path . $this->environments[$this->choices['env']]['siteFolder'], $this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old')) {
			$this->abort('Failed to move site');
		}
		$this->message(' [OK]', false, 0, true, 'ng');

		$this->message('Site: '. $this->path . $this->choices['folder'] .' '. $this->path . $this->environments[$this->choices['env']]['siteFolder'], true, -1, true, 'b0');
		if (!$this->folderMove($this->path . $this->choices['folder'], $this->path . $this->environments[$this->choices['env']]['siteFolder'])) {
			$this->abort('Failed to move site');
		}
		$this->message(' [OK]', false, 0, true, 'ng');
	}

	/**	
	 * 	Replace persistent files
	 **/
	private function replacePersistentFiles() {
		
		$this->message('Persistent File(s) -', true, 1, true, 'bb');
		$this->message('Copying Files:', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFiles'] as $k => $v) {
			if ($this->path && $this->environments[$this->choices['env']]['siteFolder'] && $v) {
				$this->message('Copying: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .' '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				$this->execute('cp -fp "'. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .'" "'. $this->path . $this->choices['folder'] . $v .'"');
				if ($this->rexec['code']) {
					$this->message(' [OK]', false, 0, true, 'ng');
					$this->counts['persistent_files']['copy'][0]++;
				} else {
					$this->message(' [FAIL]', false, 0, true, 'nr');
					$this->message($this->rexec['output'], true, -2, true, 'nr');
					$this->counts['persistent_files']['copy'][1]++;
				}
			}
		}
		$this->message('', true, 0);
	}

	/**	
	 * 	Replace persistent folders
	 **/
	private function replacePersistentFolders() {
		
		$this->message('Persistent Folder(s) -', true, 1, true, 'bb');
		$this->message('Copying Folders:', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFolders'] as $k => $v) {
			if ($this->path && $this->environments[$this->choices['env']]['siteFolder'] && $v) {
				$this->message('Copying: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .' '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				$this->execute('cp -Rfp "'. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .'" "'. $this->path . $this->choices['folder'] .'"');
				if ($this->rexec['code']) {
					$this->message(' [OK]', false, 0, true, 'ng');
					$this->counts['persistent_folders']['copy'][0]++;
				} else {
					$this->message(' [FAIL]', false, 0, true, 'nr');
					$this->message($this->rexec['output'], true, -2, true, 'nr');
					$this->counts['persistent_folders']['copy'][1]++;
				}
			}
		}
		$this->message('', true, 0);
	}

	/**	
	 * 	Create folders
	 **/
	private function createFolders() {
		
		$this->message('Creating Folder(s) -', true, 1, true, 'bb');
		$this->message('Folder(s):', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['createFolders'] as $k => $v) {
			if ($this->path && $this->choices['folder'] && $v) {
				$this->message('Creating: '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				if (!$this->pathCheck($this->choices['folder'] . $v, false, false, false)) {
					if ($this->folderCreate($this->choices['folder'] . $v)) {
						$this->message(' [OK]', false, 0, true, 'ng');
						$this->execute('echo > "'. $this->path . $this->choices['folder'] . $v .'/index.html"');
						$this->counts['create_folders']['create'][0]++;
					} else {
						$this->message(' [FAIL]', false, 0, true, 'nr');
						$this->counts['create_folders']['create'][1]++;
					}
				} else {
					$this->message(' [OK]', false, 0, true, 'ng');
				}
			}
		}
		$this->message('', true, 0);
	}

	/**	
	 * 	Remove folders
	 **/
	private function removeFolders() {
		
		$this->message('Removing Folder(s) -', true, 1, true, 'bb');
		$this->message('Folder(s):', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['removeFolders'] as $k => $v) {
			if ($this->path && $this->choices['folder'] && $v) {
				$this->message('Removing: '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				$this->folderRemove($this->path . $this->choices['folder'] . $v);
				if ($this->rexec['code']) {
					$this->message(' [OK]', false, 0, true, 'ng');
					$this->counts['remove_folders']['remove'][0]++;
				} else {
					$this->message(' [FAIL]', false, 0, true, 'nr');
					$this->counts['remove_folders']['remove'][1]++;
				}
			}
		}
		$this->message('', true, 0);
	}

	/**	
	 * 	Process compress
	 **/
	private function compress($path, $extension) {

		$compress = 'compress' . strtoupper($extension);
		return $this->$compress($path);
	}

	/**	
	 * 	Process compress PHP
	 **/
	private function compressPHP($path) {

		$content = $this->getContent($path);

		if (strlen($content) == 0) {
			return true;
		}

		$content = $this->removeComments($content);
		$content = $this->removeSpaces($content);
		
		return $this->putContent($path, $content);
	}

	/**	
	 * 	Process compress CSS
	 **/
	private function compressCSS($path) {
		
		$minifier = new Minify\CSS($path);
		$minifier->minify($path);

		return true;
	}

	/**	
	 * 	Process compress JS
	 **/
	private function compressJS($path) {
		
		$minifier = new Minify\JS($path);
		$minifier->minify($path);

		return true;
	}

	/**	
	 * 	Check sintax
	 **/
	private function syntax($path, $extension) {
		
		$syntax = 'syntax' . strtoupper($extension);
		return $this->$syntax($path);
	}

	/**	
	 * 	Check sintax PHP
	 **/
	private function syntaxPHP($path) {

		if (!shell_exec('which '. $this->environments[$this->choices['env']]['process']['php']['syntax']['app'] )) {
			$this->abort('Application not found. Please, install '. $this->environments[$this->choices['env']]['process']['php']['syntax']['app']);
		}

		$cmd = $this->environments[$this->choices['env']]['process']['php']['syntax']['app'] .' '. str_replace('{PATH}', $path, $this->environments[$this->choices['env']]['process']['php']['syntax']['string']);

		$this->execute($cmd);
		if (substr($this->rexec['output'], 0, strlen($this->environments[$this->choices['env']]['process']['php']['syntax']['expected'])) == $this->environments[$this->choices['env']]['process']['php']['syntax']['expected']) {
			return true;
		}
		return false;
	}

	/**	
	 * 	Check sintax CSS
	 **/
	private function syntaxCSS($path) {

		return true;
	}

	/**	
	 * 	Check sintax JS
	 **/
	private function syntaxJS($path) {

		//	Test
		if (!shell_exec('which '. $this->environments[$this->choices['env']]['process']['js']['syntax']['app'] )) {
			$this->abort('Application not found. Please, install '. $this->environments[$this->choices['env']]['process']['js']['syntax']['app']);
		}

		$cmd = $this->environments[$this->choices['env']]['process']['js']['syntax']['app'] .' '. str_replace('{PATH}', $path, $this->environments[$this->choices['env']]['process']['js']['syntax']['string']);

		$this->execute($cmd);
		if (substr($this->rexec['output'], 0, strlen($this->environments[$this->choices['env']]['process']['js']['syntax']['expected'])) == $this->environments[$this->choices['env']]['process']['js']['syntax']['expected']) {

		}
		if (trim($this->rexec['output']) == '') {
			return true;
		}
		return false;
	}

	/**	
	 * 	Verify files/folders
	 **/
	private function deployCheckFiles() {

		$this->qpre = 4;

		$this->message('Checking files/folders important for updating -', true, 1, true, 'bb');

		$this->message('Folder(s) site: ', true, -1, true, 'b0');
		$this->pathCheck($this->environments[$this->choices['env']]['siteFolder']);
		$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . '_old');
		$this->message('', true, 0);
		
		$this->message('Persistent file(s): ', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFiles'] as $k => $v) {
			$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . $v);
		}
		$this->message('', true, 0);
		
		$this->message('Persistent folder(s): ', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFolders'] as $k => $v) {
			$this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . $v);
		}
		$this->message('', true, 0);

		$this->message('File to deploy: ', true, -1, true, 'b0');
		$this->pathCheck($this->choices['file']);
		$this->message('', true, 0);
	}

	/**	
	 * 	Pre-Commands
	 **/
	private function preCommands() {

		$this->message('Running pre-commands -', true, 1, true, 'bb');
		foreach ($this->environments[$this->choices['env']]['preCommands'] as $k => $v) {
			$this->message($v, true, -1, true, 'b0');
			$this->execute($v);
			$this->message($this->rexec['output'], true, 0);
		}
		$this->message('', true, 0);
	}

	/**	
	 * 	Pos-Commands
	 **/
	private function posCommands() {

		$this->message('Running pos-commands -', true, 1, true, 'bb');
		foreach ($this->environments[$this->choices['env']]['posCommands'] as $k => $v) {
			$this->message($v, true, -1, true, 'b0');
			$this->execute($v);
			$this->message($this->rexec['output'], true, 0);
		}
		$this->message('', true, 0);
	}

	/**	
	 * 	Backup
	 **/
	private function backup() {

		$date = date('dmY_His');

		$this->message('Backing up the "'. $this->choices['env'] .'" site -', true, 1, true, 'bb');

		$sourceFolder = $this->environments[$this->choices['env']]['siteFolder'];
		$destinyFolder = $this->environments[$this->choices['env']]['backupFolder'] .'/'. $this->environments[$this->choices['env']]['siteFolder'] .'_'. $date;

		$this->message('Checking... ', true, -1, true, 'b0');
		
		if (!$this->folderCreate($destinyFolder)) {
			$this->abort('Could not create backup: '. $this->path . $destinyFolder);
		}
		$this->message('', true, 0);

		$this->message('Copying... ', true, -1, true, 'b0');
		$this->message('Source: '. $this->path . $sourceFolder, true, -4);
		$this->message('Destiny: '. $this->path . $destinyFolder, true, -4);
		
		$this->execute('cp -rf "'. $this->path . $sourceFolder .'" "'. $this->path . $destinyFolder .'"');

		if (!$this->pathCheck($destinyFolder)) {
			$this->abort('Could not create backup: '. $this->path . $destinyFolder);
		}
		
		$this->message('Backup completed successfully', true, -4, true, 'ng');
		$this->message('', true, 0);
	}

	/**	
	 * 	Prompt
	 **/
	private function prompt($msg, $valid = array(), $key, $test = true) {

		system('stty cbreak -echo');

		$handle = fopen("php://stdin","r");
		$prompt = '';

		$this->message('', true, -1, false, false, 'n0');
		echo shell_exec("echo -en '\r$msg'");

		while(true){
			$char = trim(fgetc($handle));
			$code = ord($char);

			if (!in_array($code, array(27))) {
			
				echo $this->execute("printf '$char';");
			
				if ($code == 127) { // Backspace
					$this->clearLine($msg);
				} else
				if ($code == 0) { // Enter
					if ($prompt != '') {
						if (!$test) {
							$this->choices[$key] = $prompt;
							break;
						} else {
							if (in_array($prompt, $valid)){
								$this->choices[$key] = $prompt;
								break;
							}
						}
					}
					$prompt = '';
					$this->clearLine($msg);
				} else {
					$prompt .= $char;
				}
			}
		}
		
		fclose($handle);
		system('stty sane');
		return true;
	}

	/**	
	 * 	Clear line
	 **/
	private function clearLine($msg) {
		echo shell_exec("echo -en '\r'; echo -en '                                                                                                    ';");
		echo shell_exec("echo -en '\r$msg'");
	}

	/**	
	 * 	Get Content of file
	 **/
	private function getContent($path) {
		return file_get_contents($path);
	}

	/**	
	 * 	Put Content of file
	 **/
	private function putContent($path, $data) {
		return file_put_contents($path, $data);
	}

	/**	
	 * 	Unzip
	 **/
	private function unzip($path, $dst = null) {
		$dst = ($dst) ? '-d "'. $dst .'"' : '';
		$this->message($this->execute('unzip -o "'. $path .'" '. $dst), true, 0, true, 'n0', $this->logfull);
	}

	/**	
	 * 	Check path
	 **/
	private function pathCheck($path, $info = true, $label = false, $die = true) {
		
		$pathFull = $this->path . $path;

		($info) ? $this->message("Checking path: '$pathFull' ", true, ($this->qpre * -1)) : null;
		if (!file_exists($pathFull)) {
			($info) ? $this->message('[FAIL] [Path not found]', false, null, true, 'br') : null;
			($label) ? $this->message($label) : null;
			($die) ? $this->abort() : null;
			return false;
		}
		($info) ? $this->message('[OK]', false, null, true, 'bg') : null;
		return true;
	}

	/**	
	 * 	Create Folder
	 **/
	private function folderCreate($path, $mask = 0755) {
		if (!mkdir($this->path . $path, $mask, true)) {
			return false;
		}
		return true;
	}

	/**	
	 * 	Remove Folder
	 **/
	private function folderRemove($path) {
		$this->execute('rm -Rf "'. $path .'"');
		return $this->rexec['code'];
	}

	/**	
	 * 	Move folder
	 **/
	private function folderMove($ori, $dest) {
		$this->execute('mv "'. $ori .'" "'. $dest .'"');
		return $this->rexec['code'];
	}

	/**	
	 * 	Permissions Folder
	 **/
	private function folderPermission($path, $mask = '0755') {
		$this->execute('chmod -R '. $mask .' "'. $path .'"');
		return $this->rexec['code'];
	}

	/**	
	 * 	Owner Folder
	 **/
	private function folderOwner($path, $user, $group) {
		if (!$user || !$group) {
			return false;
		}
		$this->execute('chown -R '. $user .':'. $group .' "'. $path .'"');
		return $this->rexec['code'];
	}

	/**	
	 * 	Navigate into folders
	 **/
	private function folder($path, $func, $extension){
		
		$dir = new DirectoryIterator($path);
		foreach ($dir as $file) {
		    if (!$file->isDot()) {
		    	if ($file->isDir()) {
		            $path = $file->getPathname();
		            foreach ($this->environments[$this->choices['env']]['process'][$extension]['ignoreFolders'] as $k => $v) {
			            if (strpos($path, $v)) {
		            		continue(2);
		            	}
		            }
		            $this->message($path, true, -6, true, 'b0', $this->logfull);
		            $this->folder($path, $func, $extension);
		        }
		        //	Is a file?
		        if ($file->isFile()) {
		            $filePath = $file->getPathname();
		            $fileName = $file->getFilename();
		            // Is a extension required ?
		            if (preg_match("/\.$extension$/", $fileName) && !in_array($fileName, $this->environments[$this->choices['env']]['process'][$extension]['ignoreFiles'])) {
		            	$this->message('File: '. $filePath, true, -8, true, 'n0', $this->logfull);
	            		if ($this->$func($filePath, $extension)) {
		            		$this->message(' [OK]', false, 0, true, 'ng', $this->logfull);
			            	$this->counts[$func][$extension][0]++;
						} else {
							$error = ">>>>>>>>>>\n". $this->rexec['output'] ."\n<<<<<<<<<<";
							$this->message(' [FAIL]', false, 0, true, 'nr', $this->logfull);
							$this->message($error, true, 0, true, 'nr', $this->logfull);
							$this->counts[$func][$extension][1]++;
			           	}
		            }
		        }
		    }
		}
	}

	/**	
	 * 	Remove comments
	 **/
	private function removeComments($content){

		$content = preg_replace('#//[a-z0-9]+$#m', "", $content);
		$content = preg_replace('![\s\t\n\r]+/\*.*?\*/!s', ' ', $content);
		$content = preg_replace('#[\s\n\t]//.+$#m', ' ', $content);
		$content = preg_replace( '![\s\t]//.*?\n!' , ' ', $content ); //
		$content = preg_replace('/<\!--.*-->/', ' ', $content);

		return $content;
	}

	/**	
	 * 	Remove spaces
	 **/
	private function removeSpaces($content){

		$content = preg_replace('/\n\s*\n/', ' ', $content);
		$content = preg_replace('/[\t\n\r]/', ' ', $content);
		//$content = preg_replace('/\r\n+/', ' ', $content);
		$content = preg_replace('/ {2,}/', ' ', $content);

		$content = preg_replace('/> </', '><', $content);
		$content = preg_replace('/=> /', '=>', $content);

		$content = preg_replace('/<<<EOF/', "<<<EOF\n", $content);
		$content = preg_replace('/<<<EOT/', "<<<EOT\n", $content);
		$content = preg_replace('/EOF;/', "\nEOF;", $content);
		$content = preg_replace('/EOT;/', "\nEOT;", $content);
		
		return $content;
	}

	/**	
	 * 	Print to screen
	 **/
	private function message($msg, $brk = true, $pre = null, $save = true, $color = null, $echo = true) {

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
		
		if ($echo) {
			echo $msg;
		}

		if ($save) {
			$this->log['clean'] .= $msgc;
			if ($echo) {
				$this->log['full'] .= $msg;
			}
		}
	}

	/**	
	 * 	Reset
	 **/
	private function reset() {
		echo $this->execute('clear');
		echo $this->log['full'];
	}

	/**	
	 * 	Execute commands
	 **/
	private function execute($command) {

		exec($command, $output, $code);
		$this->rexec['output'] = implode("\n", $output);
		$this->rexec['code'] = !$code;

		return $this->rexec['output'];
	}

	/**	
	 * 	Log
	 **/
	private function log() {
		file_put_contents(__DIR__ . '/update_'. $this->choices['folder'] .'_'. date('dmY_Hi') .'.log', $this->log['clean']);
	}

	/**	
	 * 	Abort
	 **/
	private function abort($msg = 'Execution aborted') {
		$this->message('', true, 100, true, 'br'); 
		$this->message($msg .' ---', true, 3, true, 'br');
		$this->message('', true, 100, true, 'br'); 
		$this->message('', true, -1); 

		$this->log();
		exit();
	}
}

$updater = new Updater();
$updater->start();