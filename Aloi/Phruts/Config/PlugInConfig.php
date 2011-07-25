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
 * A PHPBean representing the configuration information of a <plug-in> element
 * in a PHruts configuration file.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Config_PlugInConfig {
	/**
	 * Has this component been completely configured?
	 *
	 * @var boolean
	 */
	protected $configured = false;

	/**
	 * A map of the name-value pairs that will be used to configure the property
	 * values of a PlugIn instance.
	 *
	 * @var array
	 */
	protected $properties = array ();

	/**
	 * The fully qualified PHP class name of the PlugIn implementation
	 * class being configured.
	 *
	 * @var string
	 */
	protected $className = null;
	
	protected $key = null;
	
	public function getKey() {
		return $this->key;
	}
	
	public function setKey($key) {
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * @param string $className
	 */
	public function setClassName($className) {
		$this->className = (string) $className;
	}

	/**
	 * Add a new property name and value to the set that will be used to configure
	 * the PlugIn instance.
	 *
	 * @param string $name Property name
	 * @param string $value Property value
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function addProperty($name, $value) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$name = (string) $name;
		$this->properties[$name] = (string) $value;
	}

	/**
	 * Freeze the configuration of this component.
	 */
	public function freeze() {
		$this->configured = true;
	}

	/**
	 * Return the properties that will be used to configure a PlugIn
	 * instance.
	 *
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * Return a string representation of this object.
	 *
	 * @return string
	 */
	public function __toString() {
		$sb = 'Aloi_Phruts_Config_PlugInConfig[';
		$sb .= 'className=' . var_export($this->className, true);
		foreach ($this->properties as $name => $value) {
			$sb .= ',' . $name . '=' . var_export($value, true);
		}
		$sb .= ']';
		return $sb;
	}
}
?>
