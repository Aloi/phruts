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
 * The collection of static configuration information that describes a
 * PHruts-based module.
 *
 * Multiple modules are identified by a <em>prefix</em> in the request URI.
 * If no module prefix can be matched, the default configuration (with a
 * prefix equal to a zero-length string) is selected.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Config_ModuleConfig {
	/**
	 * The controller configuration object for this module
	 *
	 * @var ControllerConfig
	 */
	protected $controllerConfig = null;

	/**
	 * The set of action configurations for this module, if any, keyed by
	 * the path property.
	 *
	 * @var array
	 */
	protected $actionConfigs = array ();

	/**
	 * The set of form bean configurations for this module, if any, keyed by
	 * the name property.
	 *
	 * @var array
	 */
	protected $formBeans = array ();

	/**
	 * The set of global forward configurations for this module, if any,
	 * keyed by the name property.
	 *
	 * @var array
	 */
	protected $forwards = array ();

	/**
	 * The set of message resources configurations for this module, if any,
	 * keyed by the key property.
	 *
	 * @var array
	 */
	protected $messageResources = array ();

	/**
	 * The set of data source configurations for this module, if any, keyed by
	 * the key property.
	 *
	 * @var array
	 */
	protected $dataSources = array ();

	/**
	 * The set of configured plug-in Actions for this module, if any,
	 * in the order they were declared and configured.
	 *
	 * @var array
	 */
	protected $plugIns = array ();

	/**
	 * Has this application been completely configured yet.
	 *
	 * Once this flag has been set, any attempt to modify the configuration will
	 * return an Aloi_Serphlet_Exception_IllegalState.
	 *
	 * @var boolean
	 */
	protected $configured = false;

	/**
	 * The prefix of the context-relative portion of the request URI, used to
	 * select this configuration versus others supported by the controller
	 * servlet.
	 *
	 * A configuration with a prefix of a zero-length string is the default
	 * configuration for this web module.
	 *
	 * @var string
	 */
	protected $prefix = null;

	/**
	 * The default class name to be used when creating action config instances.
	 *
	 * @var string
	 */
	protected $actionConfigClass = 'Aloi_Phruts_Config_Action';

	/**
     * The set of exception handling configurations for this
     * module, if any, keyed by the <code>type</code> property.
     */
    protected $exceptions = array();
    
    /** The wildcard matcher for matching action configs to paths */
    protected $matcher;

	/**
	 * @param string $prefix The prefix of the context-relative portion of the
	 * request URI.
	 */
	public function __construct($prefix) {
		$this->prefix = (string) $prefix;
	}

	/**
	 * Has this module been completely configured yet.
	 *
	 * Once this flag has been set, any attempt to modify the configuration will
	 * return an Aloi_Serphlet_Exception_IllegalState.
	 *
	 * @return boolean
	 */
	public function getConfigured() {
		return $this->configured;
	}

	/**
	 * The controller configuration object for this module.
	 *
	 * @return ControllerConfig
	 */
	public function getControllerConfig() {
		if (is_null($this->controllerConfig)) {
			$this->controllerConfig = new Aloi_Phruts_Config_ControllerConfig();
		}
		return $this->controllerConfig;
	}

	/**
	 * The controller configuration object for this module.
	 *
	 * @param ControllerConfig $cc The controller configuration object
	 * for this module.
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function setControllerConfig(Aloi_Phruts_Config_ControllerConfig $cc) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->controllerConfig = $cc;
	}

	/**
	 * The prefix of the context-relative portion of the request URI, used to
	 * select this configuration versus others supported
	 * by the controller servlet.
	 *
	 * A configuration with a prefix of a zero-length string is the default
	 * configuration for this web module.
	 *
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * The prefix of the context-relative portion of the request URI, used to
	 * select this configuration versus others supported
	 * by the controller servlet.
	 *
	 * A configuration with a prefix of a zero-length string is the default
	 * configuration for this web module.
	 *
	 * @param string $prefix
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function setPrefix($prefix) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->prefix = (string) $prefix;
	}

	/**
	 * The default class name to be used when creating action config instances.
	 *
	 * @return string
	 */
	public function getActionClass() {
		return $this->actionConfigClass;
	}

	/**
	 * The default class name to be used when creating action config instances.
	 *
	 * @param string $actionConfigClass Default class name to be used
	 * when creating action config instances.
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function setActionClass($actionConfigClass) {
		$this->actionConfigClass = (string) $actionConfigClass;
	}

	/**
	 * Add a new Aloi_Phruts_Config_Action instance to the set associated with this
	 * module.
	 *
	 * @param Aloi_Phruts_Config_Action $config The new configuration instance
	 * to be added
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function addActionConfig(Aloi_Phruts_Config_Action $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$config->setModuleConfig($this);
		$this->actionConfigs[$config->getPath()] = $config;
	}

	/**
	 * Add a new FormBeanConfig instance to the set associated with this
	 * module.
	 *
	 * @param FormBeanConfig $config The new configuration instance
	 * to be added
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function addFormBeanConfig(Aloi_Phruts_Config_FormBeanConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->formBeans[$config->getName()] = $config;
	}

	/**
	 * Add a new ForwardConfig instance to the set of global forwards
	 * associated with this module.
	 *
	 * @param ForwardConfig $config The new configuration instance
	 * to be added
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function addForwardConfig(Aloi_Phruts_Config_ForwardConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->forwards[$config->getName()] = $config;
	}

	/**
	 * Add a new MessageResourcesConfig instance to the set associated with
	 * this module.
	 *
	 * @param MessageResourcesConfig $config The new configuration instance
	 * to be added
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function addMessageResourcesConfig(Aloi_Phruts_Config_MessageResourcesConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->messageResources[$config->getKey()] = $config;
	}

	/**
	 * Add a new DataSourceConfig instance to the set associated with
	 * this module.
	 *
	 * @param DataSourceConfig $config The new configuration instance to
	 * be added
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function addDataSourceConfig(Aloi_Phruts_Config_DataSourceConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->dataSources[$config->getKey()] = $config;
	}

	/**
	 * Add a newly configured PlugInConfig instance to the set of
	 * plug-in Actions for this module.
	 *
	 * @param PlugInConfig $plugInConfig The new configuration instance
	 * to be added
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function addPlugInConfig(Aloi_Phruts_Config_PlugInConfig $plugInConfig) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->plugIns[] = $plugInConfig;
	}

	/**
	 * Return the action configuration for the specified path, if any;
	 * otherwise return null.
	 *
	 * @param string $path The path of the action configuration to return
	 * @return Aloi_Phruts_Config_Action
	 */
	public function findActionConfig($path) {
		$path = (string) $path;

		if (array_key_exists($path, $this->actionConfigs)) {
			return $this->actionConfigs[$path];
		} else {
			// Try matching
			if(!empty($this->matcher)) {
				return $this->matcher->match($path);
			}
		}
	}

	/**
	 * Return the action configurations for this module.
	 *
	 * If there are none, a zero-length array is returned.
	 *
	 * @return array
	 */
	public function findActionConfigs() {
		return array_values($this->actionConfigs);
	}

	/**
	 * Return the form bean configuration for the specified key, if any;
	 * otherwise return null.
	 *
	 * @param string $name Name of the form bean configuration to return
	 * @return FormBeanConfig
	 */
	public function findFormBeanConfig($name) {
		$name = (string) $name;

		if (array_key_exists($name, $this->formBeans)) {
			return $this->formBeans[$name];
		} else {
			return null;
		}
	}

	/**
	 * Return the form bean configurations for this module.
	 *
	 * If there are none, a zero-length array is returned.
	 *
	 * @return array
	 */
	public function findFormBeanConfigs() {
		return array_values($this->formBeans);
	}

	/**
	 * Return the forward configuration for the specified key, if any;
	 * otherwise return null.
	 *
	 * @param string $name Name of the forward configuration to return
	 * @return ForwardConfig
	 */
	public function findForwardConfig($name) {
		$name = (string) $name;

		if (array_key_exists($name, $this->forwards)) {
			return $this->forwards[$name];
		} else {
			return null;
		}
	}

	/**
	 * Return the forward configurations for this module.
	 *
	 * If there are none, a zero-length array is returned.
	 *
	 * @return array
	 */
	public function findForwardConfigs() {
		return array_values($this->forwards);
	}

	/**
	 * Return the message resources configuration for the specified key,
	 * if any; otherwise return null.
	 *
	 * @param string $key Key of the data source configuration to return
	 * @return MessageResourcesConfig
	 */
	public function findMessageResourcesConfig($key) {
		$key = (string) $key;

		if (array_key_exists($key, $this->messageResources)) {
			return $this->messageResources[$key];
		} else {
			return null;
		}
	}

	/**
	 * Return the message resources configurations for this module.
	 *
	 * If there are none, a zero-length array is returned.
	 *
	 * @return array
	 */
	public function findMessageResourcesConfigs() {
		return array_values($this->messageResources);
	}

	/**
	 * Return the data source configuration for the specified key, if any;
	 * otherwise return null.
	 *
	 * @param string $key Key of the data source configuration to return
	 * @return DataSourceConfig
	 */
	public function findDataSourceConfig($key) {
		$key = (string) $key;

		if (array_key_exists($key, $this->dataSources)) {
			return $this->dataSources[$key];
		} else {
			return null;
		}
	}

	/**
	 * Return the data source configurations for this module.
	 *
	 * If there are none, a zero-length array is returned.
	 *
	 * @return array
	 */
	public function findDataSourceConfigs() {
		return array_values($this->dataSources);
	}

	/**
	 * Return the configured plug-in actions for this module.
	 *
	 * If there are none, a zero-length array is returned.
	 *
	 * @return array
	 */
	public function findPlugInConfigs() {
		return $this->plugIns;
	}

	/**
	 * Freeze the configuration of this module.
	 *
	 * After this method returns, any attempt to modify the configuration
	 * will return an Aloi_Serphlet_Exception_IllegalState.
	 */
	public function freeze() {
		$this->configured = true;

		$this->getControllerConfig()->freeze();

		$aconfigs = $this->findActionConfigs();
		foreach ($aconfigs as $aconfig) {
			$aconfig->freeze();
		}
		
		$this->matcher = new Aloi_Phruts_Config_ActionMatcher($aconfigs);

		$fbconfigs = $this->findFormBeanConfigs();
		foreach ($fbconfigs as $fbconfig) {
			$fbconfig->freeze();
		}

		$fconfigs = $this->findForwardConfigs();
		foreach ($fconfigs as $fconfig) {
			$fconfig->freeze();
		}

		$mrconfigs = $this->findMessageResourcesConfigs();
		foreach ($mrconfigs as $mrconfig) {
			$mrconfig->freeze();
		}

		$dsconfigs = $this->findDataSourceConfigs();
		foreach ($dsconfigs as $dsconfig) {
			$dsconfig->freeze();
		}

		$piconfigs = $this->findPlugInConfigs();
		foreach ($piconfigs as $piconfig) {
			$piconfig->freeze();
		}
	}

	/**
	 * Remove the specified action configuration instance.
	 *
	 * @param Aloi_Phruts_Config_Action $config Aloi_Phruts_Config_Action instance to be
	 * removed
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function removeActionConfig(Aloi_Phruts_Config_Action $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		unset ($this->actionConfigs[$config->getPath()]);
	}

	/**
	 * Remove the specified form bean configuration instance.
	 *
	 * @param FormBeanConfig $config FormBeanConfig instance to be
	 * removed
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function removeFormBeanConfig(Aloi_Phruts_Config_FormBeanConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		unset ($this->actionConfigs[$config->getName()]);
	}

	/**
	 * Remove the specified forward configuration instance.
	 *
	 * @param ForwardConfig $config ForwardConfig instance to be
	 * removed
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function removeForwardConfig(Aloi_Phruts_Config_ForwardConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		unset ($this->forwards[$config->getName()]);
	}

	/**
	 * Remove the specified message resources configuration instance.
	 *
	 * @param MessageResourcesConfig $config MessageResourcesConfig
	 * instance to be removed
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function removeMessageResourcesConfig(Aloi_Phruts_Config_MessageResourcesConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		unset ($this->messageResources[$config->getKey()]);
	}

	/**
	 * Remove the specified data source configuration instance.
	 *
	 * @param DataSourceConfig $config DataSourceConfig
	 * instance to be removed
	 * @throws Aloi_Serphlet_Exception_IllegalState - If this module configuration has
	 * been frozen
	 */
	public function removeDataSourceConfig(Aloi_Phruts_Config_DataSourceConfig $config) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		unset ($this->dataSources[$config->getKey()]);
	}
	
	/**
     * Add a new <code>ExceptionConfig</code> instance to the set associated
     * with this module.
     *
     * @param config The new configuration instance to be added
     *
     * @exception Aloi_Serphlet_Exception_IllegalState if this module configuration  has been
     * frozen
     */
    public function addExceptionConfig(Aloi_Phruts_Config_ExceptionConfig $config) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->exceptions[$config->getType()] = $config;
    }
    
    /**
     * Return the exception configuration for the specified type, if any;
     * otherwise return <code>null</code>.
     *
     * @param type Exception class name to find a configuration for
     * @return ExceptionConfig;
     */
    public function findExceptionConfig($type) {
		if(!empty($this->exceptions[$type])) return $this->exceptions[$type];
		return null;
    }
    
    
    /**
     * Return the exception configurations for this module.  If there
     * are none, a zero-length array is returned.
     */
    public function findExceptionConfigs() {
        return $this->exceptions;
    }
    
    /**
     * Remove the specified exception configuration instance.
     *
     * @param config Aloi_Phruts_Config_Action instance to be removed
     *
     * @exception Aloi_Serphlet_Exception_IllegalState if this module configuration  has been
     * frozen
     */
    public function removeExceptionConfig(Aloi_Phruts_Config_ExceptionConfig $config) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        unset($this->exceptions[config.getType()]);
    }
}
?>