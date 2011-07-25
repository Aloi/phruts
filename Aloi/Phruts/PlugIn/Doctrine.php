<?php
/* Copyright 2010 aloi-project
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA
 */

class Aloi_Phruts_PlugIn_Doctrine implements Aloi_Phruts_PlugIn {
	const DEFAULT_SETTINGS_FILE = 'WEB-INF/settings/doctrine.php';
	private $models = null;
	private $settings = null;
	
	/**
	 * Initialise the Doctrine Model
	 */
	public function init(Aloi_Phruts_Action_Servlet $servlet, Aloi_Phruts_Config_ModuleConfig $config) {
		static $init;
		if($init == null) {
			$log = Aloi_Util_Logger_Manager :: getLogger(__CLASS__);
			if(empty($this->settings)) $this->settings = self::DEFAULT_SETTINGS_FILE;
			
			
			// Bootstrap Doctrine
			$log->info('Initialising Doctrine: Models: ' . implode(',', $this->models) . ' Settings: ' . $this->settings);
			
			require_once('Doctrine.php');
			spl_autoload_register(array('Doctrine', 'autoload'));
			$manager = Doctrine_Manager::getInstance();
			
			// Establish DB using the DSN
			try {
				// Require the settings file
				require_once($this->settings);
				
				
				// Set the manager attributes
				if(!empty($doctrine['MANAGER'])) {
					foreach($doctrine['MANAGER'] as $property => $value) {
						if(!defined('Doctrine_Core::' . $property)) {
							$log->error('Settings defined a constant that does not exist: Doctrine_Core::' . $property);
							throw new Exception('Doctrine_Core::' . $property . ' is not defined');
						}
						if(!defined('Doctrine_Core::' . $value)) {
							$log->error('Settings defined a constant that does not exist: Doctrine_Core::' . $value);
							throw new Exception('Doctrine_Core::' . $value . ' is not defined');
						}
						$manager->setAttribute(constant('Doctrine_Core::' . $property), constant('Doctrine_Core::' . $value));
					}
				}
				
				// TODO: Support multiple database connections
				$conn = Doctrine_Manager::connection($doctrine['CONNECTION']['default']['dsn'], 'Default connection');
			} catch(Exception $e) {
				$log->error('Failed to connect to the database ' . $e->getMessage());
				throw new Exception('Failed to connect to the database, ' . $e->getMessage());
			}
			
			// Manage the models
			spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
			Doctrine_Core::loadModels($this->models);
		}
		$init = true;
	}

	public function destroy() {}
	
	/**
	 * Configure the models directory for Doctrine to locate the required models
	 * @param $directory The directory to locate the models (comma sep for multi)
	 */
	public function setModels($directory) {
		if(strpos($directory, ',') > 0) {
			// We have multiple
			$models = split(',', $directory);
		} else $models = array($directory);
		
		foreach($models as $ref => $model) {
			if(substr($model, 0, 2) == './') {
				$models[$ref] = realpath(getcwd() . substr($model, 1));
			}
		}
		$this->models = $models;
	}
	
	/**
	 * Specify the location for the doctrine settings
	 */
	public function setSettingsFile($settingsFile) {
		$this->settingsFile = $settingsFile;
	}
}