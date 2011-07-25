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
 * A PHPBean representing the configuration information of a <form-bean> element
 * in a PHruts application configuration file
 * 
 * @author Cameron MANDERSON <cameronmanderson@gmail.com> (Aloi Contributor)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Config_FormBeanConfig {
	/**
	 * Has this component been completely configured?
	 * 
	 * @var boolean
	 */
	protected $configured = false;
	
	/**
     * The set of FormProperty elements defining dynamic form properties for
     * this form bean, keyed by property name.
     */
    protected $formProperties = array();

	/**
	 * Freeze the configuration of this component.
	 */
	public function freeze() {
		$this->configured = true;
	}

	/**
	 * The module configuration with which this form bean definition
	 * is associated.
	 * 
	 * @var ModuleConfig
	 */
	protected $moduleConfig = null;

	/**
	 * Return the module configuration with which this form bean definition
	 * is associated.
	 * 
	 * @return ModuleConfig
	 */
	public function getModuleConfig() {
		return $this->moduleConfig;
	}

	/**
	 * Set the module configuration with which this form bean definition
	 * is associated.
	 * 
	 * @param ModuleConfig $moduleConfig The new ModuleConfig or
	 * null to disassociate this form bean configuration from any module
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 * @todo Check if the parameter is a ModuleConfig object.
	 */
	public function setModuleConfig($moduleConfig) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->moduleConfig = $moduleConfig;
	}

	/**
	 * The unique identifier of this form bean.
	 * 
	 * It is used to reference this bean in Aloi_Phruts_Config_Action instances as well
	 * as for the name of the request or session attribute under which the
	 * corresponding form bean instance is created or accessed.
	 * @var string
	 */
	protected $name = null;

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setName($name) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->name = (string) $name;
	}

	/**
	 * The fully qualified PHP class name of the implementation class
	 * to be used or generated.
	 * 
	 * @var string
	 */
	protected $type = null;

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setType($type) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->type = (string) $type;
	}

	 /**
     * Add a new <code>FormPropertyConfig</code> instance to the set associated
     * with this module.
     *
     * @param config The new configuration instance to be added
     *
     * @exception Aloi_Serphlet_Exception_IllegalArgument if this property name has already
     *  been defined
     */
    public function addFormPropertyConfig(Aloi_Phruts_Config_FormPropertyConfig $config) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        if (!empty($this->formProperties[$config->getName()])) {
            throw new Aloi_Serphlet_Exception_IllegalArgument("Property " + config.getName() + " already defined");
        }
        $this->formProperties[$config->getName()] = $config;
    }


    /**
     * Return the form property configuration for the specified property
     * name, if any; otherwise return <code>null</code>.
     *
     * @param name Form property name to find a configuration for
     * @return FormPropertyConfig
     */
    public function findFormPropertyConfig($name) {
        if(!empty($this->formProperties[$name])) return $this->formProperties[$name];
        return null;
    }


    /**
     * Return the form property configurations for this module.  If there
     * are none, a zero-length array is returned.
     * @return array FormPropertyConfig[]
     */
    public function findFormPropertyConfigs() {
        return $this->formProperties;
    }

	/**
	 * Return a String representation of this object.
	 * 
	 * @return string
	 */
	public function __toString() {
		$sb = 'Aloi_Phruts_Config_FormBeanConfig[';
		$sb .= 'name=' . var_export($this->name, true);
		$sb .= ',type=' . var_export($this->type, true);
		$sb .= ',properties=' . var_export(array_keys($this->formProperties), true);
		$sb .= ']';
		return $sb;
	}
}
?>
