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
 *
 * This file incorporates work covered by the following copyright and
 * permissions notice:
 *
 * Copyright (C) 2008 PHruts
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
 *
 * This file incorporates work covered by the following copyright and
 * permission notice:
 *
 * Copyright 2004 The Apache Software Foundation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * ActionServlet represents the "controller" in the Model-View-Controller
 * (MVC) design pattern for web applications that is commonly known as
 * "Model 2".
 *
 * Generally, a "Model 2" application is architected as follows:
 * <ul>
 * <li>The user interface will generally be created with PHP pages, which
 *     will not themselves contain any business logic. These pages represent
 *     the "view" component of an MVC architecture.</li>
 * <li>Forms and hyperlinks in the user interface that require business logic
 *     to be executed will be submitted to a request URI that is mapped to the
 *     controller servlet.</li>
 * <li>There will be <b>one</b> instance of this servlet class,
 *     which receives and processes all requests that change the state of
 *     a user's interaction with the application. This component represents
 *     the "controller" component of an MVC architecture.</li>
 * <li>The controller servlet will select and invoke an action class to perform
 *     the requested business logic.</li>
 * <li>The action classes will manipulate the state of the application's
 *     interaction with the user, typically by creating or modifying PHPBeans
 *     that are stored as request or session attributes (depending on how long
 *     they need to be available). Such PHPBeans represent the "model"
 *     component of an MVC architecture.</li>
 * <li>Instead of producing the next page of the user interface directly,
 *     action classes will generally use the
 *     <samp>RequestDispatcher->forward</samp> facility of the servlet
 *     API to pass control to an appropriate PHP page to produce the next page
 *     of the user interface.</li>
 * </ul>
 *
 * <p>The standard version of ActionServlet implements the
 *    following logic for each incoming HTTP request. You can override
 *    some or all of this functionality by subclassing this servlet and
 *    implementing your own version of the processing.</p>
 * <ul>
 * <li>Identify, from the incoming request URI, the substring that will be
 *     used to select an action procedure.</li>
 * <li>Use this substring to map to the PHP class name of the corresponding
 *     action class (an implementation of the Action interface).
 *     </li>
 * <li>If this is the first request for a particular action class, instantiate
 *     an instance of that class and cache it for future use.</li>
 * <li>Optionally populate the properties of a Aloi_Phruts_Action_Form bean
 *     associated with this mapping.</li>
 * <li>Call the <samp>execute</samp> method of this action class, passing
 *     on a reference to the mapping that was used (thereby providing access
 *     to the underlying ActionServlet and ServletContext, as
 *     well as any specialized properties of the mapping itself), and the
 *     request and response that were passed to the controller.</li>
 * </ul>
 *
 * @author Cameron MANDERSON <cameronmanderson@gmail.com>
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 * @todo Add information in the class comment about the web deployment
 * descriptor "/WEB-INF/web.xml".
 * @todo Manage the possibility of subclassing the servlet controller.
 */
class Aloi_Phruts_Action_Servlet extends Aloi_Serphlet_Http_Servlet {

	/**
	 * Comma-separated list of context-relative path(s) to our configuration
	 * resource(s) for the default module.
	 *
	 * @var string
	 */
	protected $config = '/WEB-INF/phruts-config.xml';

	/**
	 * The digester used to produce ModuleConfig object from
	 * a PHruts configuration file.
	 *
	 * @var Digester
	 */
	protected $configDigester = null;

	/**
	 * The resources object for our internal resources.
	 *
	 * @var PropertyMessageResources
	 */
	protected $internal = null;

	/**
	 * The PHP base name of our internal resources.
	 *
	 * @var string
	 */
	protected $internalName = 'Aloi_Phruts_Action_ActionResources';

	/**
	 * Logging instance.
	 *
	 * @var Logger
	 */
	protected static $log = null;

	/**
	 * The RequestProcessor instance we will use to process all incoming
	 * requests.
	 *
	 * @var RequestProcessor
	 */
	protected $processor = null;

	/**
	 * The factories data sources that has been configured for this module,
	 * if any.
	 *
	 * @var array
	 */
	protected $dataSourceFactories = array ();
	
	/**
	 * Set the default config prefix for the config rule set
	 */
	protected $configPrefix = 'phruts-config';

	public function __wakeup() {
		if (is_null(self :: $log)) {
			self :: $log = Aloi_Util_Logger_Manager :: getLogger(__CLASS__);
		}
	}

	/**
	 * @param ServletContext $context
	 */
	public function __construct() {
		if (is_null(self :: $log)) {
			self :: $log = Aloi_Util_Logger_Manager :: getLogger(__CLASS__);
		}
	}


	/**
	 * Return the MessageResources instance containing our internal message
	 * strings.
	 *
	 * @return MessageResources
	 */
	public function getInternal() {
		if (is_null($this->internal)) {
			$this->initInternal();
		}
		return $this->internal;
	}

	/**
	 * Initialize this servlet.
	 *
	 * Most of processing has been factored into support methods so that you can
	 * override particular functionality at a fairly granular level.
	 *
	 * @exception Aloi_Serphlet_Exception - If we cannot configure ourselves
	 * correctly
	 */
	public function init(Aloi_Serphlet_Config_ServletConfig $servletConfig) {
		try {
			// Set the config
			parent::init($servletConfig);
			
			$this->initInternal();
			$this->initServlet();
			
			// Initialize modules as needed
			$this->getServletContext()->setAttribute(Aloi_Phruts_Globals :: ACTION_SERVLET_KEY, $this);
			
			// Determine the config specs
			$moduleConfigSpecs = array();
			$moduleConfigSpecs[] = array('prefix' => '', 'config' => $this->config);
			$names = $this->getServletConfig()->getInitParameterNames();
			$prefixes = array ();
			$configSet = false;
			if(!empty($names)) foreach ($names as $name) {
				if (substr($name, 0, 7) != 'config/')
					continue;
				$prefix = substr($name, 6);
				$prefixes[] = $prefix;
				$moduleConfigSpecs[] = array('prefix' => $prefix, 'config' => $this->getServletConfig()->getInitParameter($name));
			}
			// Load the module configs
			$this->initModuleConfigs($moduleConfigSpecs);
			if (!empty ($prefixes)) {
				$this->getServletContext()->setAttribute(Aloi_Phruts_Globals :: PREFIXES_KEY, $prefixes);
			}

			$this->configDigester = null;
		} catch (Aloi_Serphlet_Exception $e) {
			throw $e;
		}
	}
	
	private function initModuleConfigs($moduleConfigSpecs) {
		// Determine if any configs have changed
		$cacheExpired = false;
		foreach($moduleConfigSpecs as $moduleConfigSpec) {
			$paths = split(',', $moduleConfigSpec['config']);
			foreach($paths as $path) {
				if($this->configurationExpired($path)) {
					$cacheExpired = true;
					break;
				}
			}
		}
		
		// Check the action
		if($cacheExpired) {
			// We need to rebuild our config specs
			$moduleConfigs = array();
			foreach($moduleConfigSpecs as $moduleConfigSpec) {
				$moduleConfig = $this->initModuleConfig($moduleConfigSpec['prefix'], $moduleConfigSpec['config']);
				$moduleConfig->freeze();
				$moduleConfigs[$moduleConfigSpec['prefix']] = $moduleConfig;
			}
			
			// Write the config cache
			$cacheFile = Aloi_Serphlet_Host::getRealPath(Aloi_Serphlet_Host::getCacheDirectory() . DIRECTORY_SEPARATOR . 'phruts.data');
			$serialData = serialize($moduleConfigs);
			if(is_writable(Aloi_Serphlet_Host::getCacheDirectory())) {
				file_put_contents($cacheFile, $serialData);
			}
		} else {
			// Load the configs
			$cacheFile = Aloi_Serphlet_Host::getRealPath(Aloi_Serphlet_Host::getCacheDirectory() . DIRECTORY_SEPARATOR . 'phruts.data');
			$serialData = file_get_contents($cacheFile);
			$moduleConfigs = unserialize($serialData);
		}
		
		foreach($moduleConfigs as $prefix => $moduleConfig) {
			$this->getServletContext()->setAttribute(Aloi_Phruts_Globals :: MODULE_KEY . $prefix, $moduleConfig);
			$this->initModuleMessageResources($moduleConfig);
			$this->initModuleDataSources($moduleConfig);
			$this->initModulePlugIns($moduleConfig);
		}
	}
	
	/**
	 * Determine if the config has changed more recently than our cached file
	 * @param unknown_type $config
	 */
	private function configurationExpired($config) {
		static $cacheTime;
		if(empty($cacheTime)) {
			$cachePath = Aloi_Serphlet_Host::getRealPath(Aloi_Serphlet_Host::getCacheDirectory() . DIRECTORY_SEPARATOR . 'phruts.data');
			if (!file_exists($cachePath)) {
				return true;
			}
			$cacheTime = filemtime($cachePath);
		}
		
		// Compare the cache file
		$filePath = Aloi_Serphlet_Host::getRealPath($config); // Pop the first
		$fileTime = filemtime($filePath);
		
		return $fileTime > $cacheTime;
	}

	/**
	 * Initialize our internal message resources bundle.
	 *
	 * @exception Aloi_Serphlet_Exception - If we cannot initialize these
	 * resources
	 */
	protected function initInternal() {
		// Create message resources
		$factory = Aloi_Phruts_Util_MessageResourcesFactory :: createFactory();
		if (is_null($factory)) {
			$msg = 'Cannot load internal resources from "' . $this->internalName . '"';
			self :: $log->error($msg);
			throw new Aloi_Serphlet_Exception($msg);
		}

		$this->internal = $factory->createResources($this->internalName);
	}

	/**
	 * Initialize global characteristics of the controller servlet.
	 */
	protected function initServlet() {
		$value = $this->getServletConfig()->getInitParameter('config');
		if (!is_null($value)) {
			$this->config = $value;
		}
	}

	/**
	 * Initialize the application configuration information
	 * for the specified module.
	 *
	 * @param string $prefix Module prefix for this module
	 * @param string $paths Comma-separated list of context-relative resource
	 * path(s) for this module's configuration resource(s).
	 * @return ModuleConfig The new module configuration instance.
	 * @throws Aloi_Serphlet_Exception - If initialization cannot be performed
	 * @todo Check if $paths is empty.
	 */
	protected function initModuleConfig($prefix, $paths) {
		if (self :: $log->isDebugEnabled()) {
			self :: $log->debug('Initializing module "' . $prefix . '" configuration from "' . $paths . '"');
		}

		// Parse the configuration for this module
		$config = new Aloi_Phruts_Config_ModuleConfig($prefix);

		// Configure the Digester instance we will use
		$digester = $this->initConfigDigester();

		// Process each specified resource path
		$temps = explode(',', $paths);
		foreach ($temps as $path) {
			$digester->push($config);
			try {
				$realPath = $this->getServletContext()->getRealPath($path);
				$digester->parse($realPath);
			} catch (Exception $e) {
				$msg = $this->internal->getMessage(null, 'configParse', $paths);
				self :: $log->error($msg . ' - ' . $e->getMessage());
				throw new Aloi_Serphlet_Exception($msg);
			}
		}
//		$this->getServletContext()->setAttribute(Aloi_Phruts_Globals :: MODULE_KEY . $prefix, $config);

		// Return the completed configuration object
		return $config;
	}

	/**
	 * Create (if needed) and return a new Digester instance that has been
	 * initialized to process PHruts module configuration file and
	 * configure a corresponding ModuleConfig object (which must be
	 * pushed on to the evaluation stack before parsing begins).
	 *
	 * @return Digester A new configured Digester instance.
	 */
	protected function initConfigDigester() {
		// Do we have an existing instance?
		if (!is_null($this->configDigester)) {
			return $this->configDigester;
		}
		
		// Obtain the configuration prefix (as optional parameter)
		$configPrefix = $this->getServletConfig()->getInitParameter('configPrefix');
		if(empty($configPrefix)) $configPrefix = $this->configPrefix;
		$this->configPrefix = $configPrefix;

		// Create a new Digester instance with standard capabilities
		$this->configDigester = new Aloi_Phigester_Digester();
		$this->configDigester->addRuleSet(new Aloi_Phruts_Config_ConfigRuleSet($this->configPrefix));
		
		return $this->configDigester;
	}

	/**
	 * Initialize the application message resources for the specified module.
	 *
	 * @param ModuleConfig $config ModuleConfig information for
	 * this module
	 * @exception Aloi_Serphlet_Exception - If initialization cannot be performed
	 */
	protected function initModuleMessageResources(Aloi_Phruts_Config_ModuleConfig $config) {
		$mrcs = $config->findMessageResourcesConfigs();
		foreach ($mrcs as $mrc) {
			if (self :: $log->isDebugEnabled()) {
				self :: $log->debug('Initializing module "' . $config->getPrefix() . '" message resources from "' . $mrc->getParameter() . '"');
			}

			$factory = $mrc->getFactory();
			Aloi_Phruts_Util_MessageResourcesFactory :: setFactoryClass($factory);
			$factoryObject = Aloi_Phruts_Util_MessageResourcesFactory :: createFactory($factory);
			if (is_null($factoryObject)) {
				$msg = 'Cannot load resources from "' . $mrc->getParameter() . '"';
				self :: $log->error($msg);
				throw new Aloi_Serphlet_Exception($msg);
			}

			$resources = $factoryObject->createResources($mrc->getParameter());
			$resources->setReturnNull($mrc->getNull());
			$this->getServletContext()->setAttribute($mrc->getKey() . $config->getPrefix(), $resources);
		}
	}

	/**
	 * Initialize the data sources for the specified module.
	 *
	 * @param ModuleConfig $config ModuleConfig information for
	 * this module
	 * @throws Aloi_Serphlet_Exception - If initialization cannot be performed
	 */
	protected function initModuleDataSources(Aloi_Phruts_Config_ModuleConfig $config) {
		if (self :: $log->isDebugEnabled()) {
			self :: $log->debug('Initialization module path "' . $config->getPrefix() . '" data sources');
		}

		$dscs = $config->findDataSourceConfigs();
		foreach ($dscs as $dsc) {
			if (self :: $log->isDebugEnabled()) {
				self :: $log->debug('Initialization module path "' . $config->getPrefix() . '" data source "' . $dsc->getKey() . '"');
			}

			try {
				Aloi_Phruts_Util_DataSourceFactory :: setFactoryClass($dsc->getType());
				$dsFactory = Aloi_Phruts_Util_DataSourceFactory :: createFactory($dsc);

				//API :: addInclude($dsc->getType());
			} catch (Exception $e) {
				$msg = $this->internal->getMessage(null, 'dataSource.init', $dsc->getKey());
				self :: $log->error($msg . ' - ' . $e->getMessage());
				throw new Aloi_Serphlet_Exception($msg);
			}
			$this->dataSourceFactories[$dsc->getKey() . $config->getPrefix()] = $dsFactory;
		}
	}

	/**
	 * Initialize the plug ins for the specified module.
	 *
	 * @param ModuleConfig $config ModuleConfig information
	 * for this module
	 * @throws Aloi_Serphlet_Exception - If initialization cannot be performed
	 */
	protected function initModulePlugIns(Aloi_Phruts_Config_ModuleConfig $config) {
		if (self :: $log->isDebugEnabled()) {
			self :: $log->debug('Initializing module "' . $config->getPrefix() . '" plug ins');
		}

		$plugInConfigs = $config->findPlugInConfigs();
		$plugIns = array ();
		foreach ($plugInConfigs as $plugInConfig) {
			try {
				$plugIn = Aloi_Serphlet_ClassLoader :: newInstance($plugInConfig->getClassName(), 'Aloi_Phruts_PlugIn');
				Aloi_Phruts_Util_BeanUtils :: populate($plugIn, $plugInConfig->getProperties());
				$plugIn->init($this, $config);

				$plugIns[] = $plugIn;
			} catch (Exception $e) {
				$msg = $this->internal->getMessage(null, 'plugIn.init', $plugInConfig->getClassName());
				self :: $log->error($msg . ' - ' . $e->getMessage());
				throw new Aloi_Serphlet_Exception($msg);
			}
		}
		$this->getServletContext()->setAttribute(Aloi_Phruts_Globals :: PLUG_INS_KEY . $config->getPrefix(), $plugIns);
	}

	/**
	 * Perform the standard request processing for this request, and create
	 * the corresponding response.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @throws Aloi_Serphlet_Exception
	 */
	protected function process(Aloi_Serphlet_Request $request, Aloi_Serphlet_Response $response) {
		// Include the boot configuration for setting up modules
		Aloi_Phruts_Util_RequestUtils::selectModule($request, $this->getServletContext());
		try {
			$this->getRequestProcessor($this->getModuleConfig($request))->process($request, $response);
		} catch (Exception $e) {
			throw new Aloi_Serphlet_Exception($e->getMessage());
		}
	}
	
	public function doGet(Aloi_Serphlet_Http_Request $request, Aloi_Serphlet_Http_Response $response) {
		$this->process($request, $response);
	}
	
	public function doPost(Aloi_Serphlet_Http_Request $request, Aloi_Serphlet_Http_Response $response) {
		$this->process($request, $response);
	}

	/**
	 * Return the module configuration object for the currently selected module.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @return ModuleConfig
	 */
	protected function getModuleConfig(Aloi_Serphlet_Application_HttpRequest $request) {
		$config = $request->getAttribute(Aloi_Phruts_Globals :: MODULE_KEY);
		if (is_null($config)) {
			$config = $this->getServletContext()->getAttribute(Aloi_Phruts_Globals :: MODULE_KEY);
		}
		return $config;
	}

	/**
	 * Look up and return the RequestProcessor responsible for the
	 * specified module, creating a new one if necessary.
	 *
	 * @param ModuleConfig $config The module configuration for which
	 * to acquire and return a RequestProcessor.
	 * @return RequestProcessor
	 * @exception Aloi_Serphlet_Exception - If we cannot instantiate
	 * a RequestProcessor instance
	 */
	protected function getRequestProcessor(Aloi_Phruts_Config_ModuleConfig $config) {
		$key = Aloi_Phruts_Globals :: REQUEST_PROCESSOR_KEY . $config->getPrefix();
		$processor = $this->getServletContext()->getAttribute($key);

		if (is_null($processor)) {
			try {
				$processorClass = $config->getControllerConfig()->getProcessorClass();
				$processor = Aloi_Serphlet_ClassLoader :: newInstance($processorClass, 'Aloi_Phruts_RequestProcessor');

//				API :: addInclude($processorClass);
			} catch (Exception $e) {
				throw new Aloi_Serphlet_Exception('Cannot initialize RequestProcessor of class ' . $processorClass . ': ' . $e->getMessage());
			}
			$processor->init($this, $config);
			$this->getServletContext()->setAttribute($key, $processor);
		}

		return $processor;
	}

	/**
	 * Return the specified data source for the current module.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param string $key The key specified in the <data-source> element for
	 * the requested data source
	 * @return object
	 * @throws Exception
	 * @todo Throws an exception if key doesn't correspond to a data source.
	 */
	public function getDataSource(Aloi_Serphlet_Application_HttpRequest $request, $key) {

		// Identify the current module
		$moduleConfig = Aloi_Phruts_Util_RequestUtils :: getModuleConfig($request, $this->getServletContext());

		// Return the requested data source instance
		$keyPrefixed = $key . $moduleConfig->getPrefix();
		$dataSource = $request->getAttribute($keyPrefixed);
		if (is_null($dataSource)) {
			if (!array_key_exists($keyPrefixed, $this->dataSourceFactories)) {
				return null;
			}
			$dsFactory = $this->dataSourceFactories[$keyPrefixed];
			try {
				$dataSource = $dsFactory->createDataSource();
			} catch (Exception $e) {
				throw $e;
			}
			$request->setAttribute($keyPrefixed, $dataSource);
		}
		return $dataSource;
	}
}