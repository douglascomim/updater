<?php
/**	
 * 	System updater manager
 *	@author Douglas Comim <douglas.comim@gmail.com>
 *	@version 2.0 | 19.11.2018
 **/ 
class Updater {

	//	Path execution
	private $path = __DIR__;
	
	//	Breakline chars
	private $breakLine = "\n";

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
		$this->header();
	}

	/**	
	 * 	Header
	 **/
	private function header() {
		$this->print('Header');
	}

	/**	
	 * 	Print to screen
	 *	@param String $message
	 **/
	private function print($message) {
		echo $message;
	}	
}

$updater = new Updater();
$updater->start();