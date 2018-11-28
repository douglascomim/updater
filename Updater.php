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

	//	Return Exec
	private $rexec = array('output','code');

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
			'type' => 'deploy',						// Type of update: [deploy: use master.zip | site: copy source from site]
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
				//'/etc/init.d/network status',
			),
			'posCommands' => array(
				//'sudo /etc/init.d/apache2 start',
			),
			'process' => array(
				'css' => array(
					'enable' => true,
					'process' => array('compress'),
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
					'process' => array('compress'),
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
	 **/
	private function body() {
		$this->qpre = 2; 
		
		$this->print('', true, 100, true, 'bb');
		$this->print('Gathering Information for Upgrade', true, 0, true, 'bb');
		$this->print('', true, 100, true, 'bb');
		
		$this->print('Which environment do you want to upgrade -', true, 1, true, 'bb');
		$this->prompt('['. implode(' / ', $this->envs) .'] : ', $this->envs, 'env');
		$this->print('', true, 0);
		
		$method = $this->environments[$this->choices['env']]['type'];
		$this->$method();
		
		//	Finish
		$this->print('', true, 0);
		$this->print('', true, 100, true, 'bb');
		$this->print('Finished update process', true, -1, true, 'bb');
		$this->print('', true, 100, true, 'bb');
	}

	/**	
	 * 	Update from deploy
	 **/
	private function deploy() {
		
		$this->print('Archive for deploy -', true, 1, true, 'bb');
		$this->prompt('', array(), 'file', false);
		$this->choices['folder'] = substr($this->choices['file'], 0, -4);
		
		//	Summary
		$this->print('', true, 100, true, 'bb');
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
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['createFolders']) .']', true, -4);
		$this->print('', true, 0);

		$this->print('Folders to be removed: ', true, -1, true, 'b0');
		$this->print('['. implode(', ', $this->environments[$this->choices['env']]['removeFolders']) .']', true, -4);
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
		
		$this->print('', true, 100, true, 'bb');

		$this->print('Do you want to proceed with the update? -', true, 1, true, 'bb');
		$this->prompt('[Y/N] : ', array('Y','N'), 'confirm');
		
		if ($this->choices['confirm'] != 'Y'){
			$this->abort('Upgrade canceled by user');
		}
		
		$this->print('', true, 0);

		$this->print('', true, 100, true, 'bb');
		$this->print('Starting update process ...', true, 0, true, 'bb');
		$this->print('', true, 100, true, 'bb');

		//	Verifing files/folders
		$this->deployCheckFiles();
		
		//	Backup
		$this->backup();

		//	Pre-commands
		$this->preCommands();

		//	Unzip Files
		$this->print('Decompressing source files -', true, 1, true, 'bb');
		$this->print('Unzip:', true, -1, true, 'b0');
		$this->print($this->path . $this->choices['file'] .' -> '. $this->path, true, -4);
		$this->unzip($this->path . $this->choices['file'], $this->path);
		$this->print('', true, 0);
		
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

		$this->print('', true, 100, true, 'bb');
		
		//	Remove old site
		$this->removeSiteOld();
		
		//	Move new sites
		$this->moveSites();
		
		//	Remove files of installation
		$this->removeFiles();

	}

	/**	
	 * 	Deploy process files/folders
	**/
	public function deployProcessFiles() {
		
		$this->print('Processing files -', true, 1, true, 'bb');
		$this->print('Compressing: ', true, -1, true, 'b0');
		
		$action = 'compress';
		foreach ($this->environments[$this->choices['env']]['process'] as $k => $v) {
			if ($v['enable']) {
				$this->print(strtoupper($k), true, -4, true, 'b0');
				if (!in_array($action, $this->environments[$this->choices['env']]['process'][$k]['process'])) {
					$this->print("Process '$action' not enabled", true, -8, true, 'b0');
					continue(1);
				}
				foreach ($v['source'] as $kk => $vv) {
					$this->print($vv, true, -6, true, 'b0');
					$this->folder($this->path . $this->choices['folder'] . $vv, $action, $k);
				}
			}
		}

		$this->print('', true, 0);
		$this->print('Testing syntax: ', true, -1, true, 'b0');
		
		$action = 'syntax';
		foreach ($this->environments[$this->choices['env']]['process'] as $k => $v) {
			if ($v['enable']) {
				$this->print(strtoupper($k), true, -4, true, 'b0');
				if (!in_array($action, $this->environments[$this->choices['env']]['process'][$k]['process'])) {
					$this->print("Process '$action' not enabled", true, -8, true, 'b0');
					continue(1);
				}
				foreach ($v['source'] as $kk => $vv) {
					$this->print($vv, true, -6);
					$this->folder($this->path . $this->choices['folder'] . $vv, $action, $k);
				}
			}
		}

		$this->print('', true, 0);
		$this->print('Changing Permissions -', true, 1, true, 'bb');
		$this->print('Folder:', true, -1, true, 'b0');
		$this->print('Permissions: '. $this->environments[$this->choices['env']]['maskFolder'], true, -4, true, 'n0');
		$this->print('Changing: '. $this->path . $this->choices['folder'], true, -4, true, 'n0');
		
		if (!$this->pathCheck($this->choices['folder'], false, false, false)) {
			$this->print(' [FAIL]', false, 0, true, 'nr');
			$this->abort('Folder not found.');
		}
		if ($this->folderPermission($this->path . $this->choices['folder'], $this->environments[$this->choices['env']]['maskFolder'])) {
			$this->print(' [OK]', false, 0, true, 'ng');
		} else {
			$this->print(' [FAIL]', false, 0, true, 'nr');
		}
		
		$this->print('', true, 0);
		$this->print('Changing Owner -', true, 1, true, 'bb');
		$this->print('Folder:', true, -1, true, 'b0');
		$this->print('Owner: '. $this->environments[$this->choices['env']]['user'] .':'. $this->environments[$this->choices['env']]['user'], true, -4, true, 'n0');
		$this->print('Changing: '. $this->path . $this->choices['folder'], true, -4, true, 'n0');
		
		if (!$this->pathCheck($this->choices['folder'], false, false, false)) {
			$this->print(' [FAIL]', false, 0, true, 'nr');
			$this->abort('Folder not found.');
		}
		if ($this->folderOwner($this->path . $this->choices['folder'], $this->environments[$this->choices['env']]['user'], $this->environments[$this->choices['env']]['user'])) {
			$this->print(' [OK]', false, 0, true, 'ng');
		} else {
			$this->print(' [FAIL]', false, 0, true, 'nr');
		}

		$this->print('', true, 0);
		$this->print('', true, 100, true, 'bb');
		$this->print('Summary of processing -', true, 1, true, 'bb');

		foreach ($this->counts as $k => $v) {
			$this->print(strtoupper($k), true, -4, true, 'b0');
			foreach ($this->counts[$k] as $kk => $vv) {
				$this->print(str_pad(strtoupper($kk), 25), true, -8, true, 'b0');
				$this->print("\t". $vv[1], false, 0, true, 'br');
				$this->print(' / ', false, 0);
				$this->print($vv[0], false, 0, true, 'bg');
				$this->print(' / ', false, 0);
				$this->print(($vv[0] + $vv[1]), false, 0, true, 'b0');
			}
		}

		$this->print('', true, 100, true, 'bb');
		$this->print('Do you want to continue the update? -', true, 1, true, 'bb');
		$this->prompt('[Y/N] : ', array('Y','N'), 'confirm');
		if ($this->choices['confirm'] != 'Y'){
			$this->abort('Upgrade canceled by user');
		}
	}

	/**	
	 * 	Remove files
	 **/
	public function removeFiles() {

		$this->print('', true, 0);
		$this->print('Removing files(s) -', true, 1, true, 'bb');
		$this->print('File: '. $this->path . $this->choices['file'], true, -1, true, 'b0');
		$this->execute('rm '.$this->path . $this->choices['file']);
		if (!$this->rexec['code']) {
			$this->print(' [FAIL]', false, 0, true, 'nr');
		}
		$this->print(' [OK]', false, 0, true, 'ng');
	}

	/**	
	 * 	Remove Old site
	 **/
	public function removeSiteOld() {

		$this->print('', true, 0);
		$this->print('Removing Old Site(s) -', true, 1, true, 'bb');
		$this->print('Site: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old', true, -1, true, 'b0');
		if ($this->pathCheck($this->environments[$this->choices['env']]['siteFolder'] . '_old', false, false, false)) {
			if (!$this->folderRemove($this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old')) {
				$this->abort('Failed to delete old site');
			}
		}
		$this->print(' [OK]', false, 0, true, 'ng');
	}

	/**	
	 * 	Move sites
	 **/
	public function moveSites() {

		$this->print('', true, 0);
		$this->print('Moving Site(s) -', true, 1, true, 'bb');
		$this->print('Site: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] .' '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old', true, -1, true, 'b0');
		if (!$this->folderMove($this->path . $this->environments[$this->choices['env']]['siteFolder'], $this->path . $this->environments[$this->choices['env']]['siteFolder'] .'_old')) {
			$this->abort('Failed to move site');
		}
		$this->print(' [OK]', false, 0, true, 'ng');

		$this->print('Site: '. $this->path . $this->choices['folder'] .' '. $this->path . $this->environments[$this->choices['env']]['siteFolder'], true, -1, true, 'b0');
		if (!$this->folderMove($this->path . $this->choices['folder'], $this->path . $this->environments[$this->choices['env']]['siteFolder'])) {
			$this->abort('Failed to move site');
		}
		$this->print(' [OK]', false, 0, true, 'ng');
	}

	/**	
	 * 	Replace persistent files
	 **/
	public function replacePersistentFiles() {
		
		$this->print('Persistent File(s) -', true, 1, true, 'bb');
		$this->print('Copying Files:', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFiles'] as $k => $v) {
			if ($this->path && $this->environments[$this->choices['env']]['siteFolder'] && $v) {
				$this->print('Copying: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .' '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				$this->execute('cp -fp "'. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .'" "'. $this->path . $this->choices['folder'] . $v .'"');
				if ($this->rexec['code']) {
					$this->print(' [OK]', false, 0, true, 'ng');
					$this->counts['persistent_files']['copy'][0]++;
				} else {
					$this->print(' [FAIL]', false, 0, true, 'nr');
					$this->print($this->rexec['output'][0], true, -2, true, 'nr');
					$this->counts['persistent_files']['copy'][1]++;
				}
			}
		}
		$this->print('', true, 0);
	}

	/**	
	 * 	Replace persistent folders
	 **/
	public function replacePersistentFolders() {
		
		$this->print('Persistent Folder(s) -', true, 1, true, 'bb');
		$this->print('Copying Folders:', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['persistentFolders'] as $k => $v) {
			if ($this->path && $this->environments[$this->choices['env']]['siteFolder'] && $v) {
				$this->print('Copying: '. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .' '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				$this->execute('cp -Rfp "'. $this->path . $this->environments[$this->choices['env']]['siteFolder'] . $v .'" "'. $this->path . $this->choices['folder'] .'"');
				if ($this->rexec['code']) {
					$this->print(' [OK]', false, 0, true, 'ng');
					$this->counts['persistent_folders']['copy'][0]++;
				} else {
					$this->print(' [FAIL]', false, 0, true, 'nr');
					$this->print($this->rexec['output'][0], true, -2, true, 'nr');
					$this->counts['persistent_folders']['copy'][1]++;
				}
			}
		}
		$this->print('', true, 0);
	}

	/**	
	 * 	Create folders
	 **/
	public function createFolders() {
		
		$this->print('Creating Folder(s) -', true, 1, true, 'bb');
		$this->print('Folder(s):', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['createFolders'] as $k => $v) {
			if ($this->path && $this->choices['folder'] && $v) {
				$this->print('Creating: '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				if (!$this->pathCheck($this->choices['folder'] . $v, false, false, false)) {
					if ($this->folderCreate($this->choices['folder'] . $v)) {
						$this->print(' [OK]', false, 0, true, 'ng');
						$this->execute('echo > "'. $this->path . $this->choices['folder'] . $v .'/index.html"');
						$this->counts['create_folders']['create'][0]++;
					} else {
						$this->print(' [FAIL]', false, 0, true, 'nr');
						$this->counts['create_folders']['create'][1]++;
					}
				} else {
					$this->print(' [OK]', false, 0, true, 'ng');
				}
			}
		}
		$this->print('', true, 0);
	}

	/**	
	 * 	Remove folders
	 **/
	public function removeFolders() {
		
		$this->print('Removing Folder(s) -', true, 1, true, 'bb');
		$this->print('Folder(s):', true, -1, true, 'b0');
		foreach ($this->environments[$this->choices['env']]['removeFolders'] as $k => $v) {
			if ($this->path && $this->choices['folder'] && $v) {
				$this->print('Removing: '. $this->path . $this->choices['folder'] . $v, true, -4, true, 'n0');
				$this->folderRemove($this->path . $this->choices['folder'] . $v);
				if ($this->rexec['code']) {
					$this->print(' [OK]', false, 0, true, 'ng');
					$this->counts['remove_folders']['remove'][0]++;
				} else {
					$this->print(' [FAIL]', false, 0, true, 'nr');
					$this->counts['remove_folders']['remove'][1]++;
				}
			}
		}
		$this->print('', true, 0);
	}

	/**	
	 * 	Process compress
	 **/
	public function compress($path, $filename, $extension) {

		$content = $this->getContent($path);

		if (strlen($content) == 0) {
			return true;
		}

		$content = $this->removeComments($content);
		$content = $this->removeSpaces($content);
		
		return $this->putContent($path, $content);
	}

	/**	
	 * 	Check sintax
	 **/
	public function syntax($path, $filename, $extension) {

		$this->execute("php -l '$path'");
		if (substr($this->rexec['output'][0], 0, 25) == 'No syntax errors detected') {
			return true;
		}
		return false;
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
			$this->execute($v);
			$this->print($this->rexec['output'][0], true, 0);
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
	 * 	Get Content of file
	 **/
	public function getContent($path) {
		return file_get_contents($path);
	}

	/**	
	 * 	Put Content of file
	 **/
	public function putContent($path, $data) {
		return file_put_contents($path, $data);
	}

	/**	
	 * 	Unzip
	 **/
	public function unzip($path, $dst = null) {
		$dst = ($dst) ? '-d "'. $dst .'"' : '';
		//$this->execute('unzip -o "'. $path .'" '. $dst .' > /dev/null 2>&1');
		$this->execute('unzip -o "'. $path .'" '. $dst);
	}

	/**	
	 * 	Check path
	 **/
	private function pathCheck($path, $info = true, $label = false, $die = true) {
		
		$pathFull = $this->path . $path;

		($info) ? $this->print("Checking path: '$pathFull' ", true, ($this->qpre * -1)) : null;
		if (!file_exists($pathFull)) {
			($info) ? $this->print('[FAIL] [Path not found]', false, null, true, 'br') : null;
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
	public function folder($path, $func, $extension){
		
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
		            $this->print($path, true, -6, true, 'b0');
		            $this->folder($path, $func, $extension);
		        }
		        //	Is a file?
		        if ($file->isFile()) {
		            $filePath = $file->getPathname();
		            $fileName = $file->getFilename();
		            // Is a extension required ?
		            if (preg_match("/\.$extension$/", $fileName) && !in_array($fileName, $this->environments[$this->choices['env']]['process'][$extension]['ignoreFiles'])) {
		            	$this->print('File: '. $filePath, true, -8);
	            		if ($this->$func($filePath, $fileName, $extension)) {
		            		$this->print(' [OK]', false, 0, true, 'bg');
			            	$this->counts[$func][$extension][0]++;
			           	} else {
			           		$this->print(' [FAIL]', false, 0, true, 'br');
			            	$this->counts[$func][$extension][1]++;
			           		if ($this->terror) {
			           			$this->print($this->terror, true, -8, true, 'br');
			           		}
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

		$return = exec($command, $output, $code);
		$this->rexec['output'] = $output;
		$this->rexec['code'] = !$code;

		return $return;
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
		$this->print($msg .' ---', true, 3, true, 'br');
		$this->print('', true, 100, true, 'br'); 
		exit();
	}
}

$updater = new Updater();
$updater->start();