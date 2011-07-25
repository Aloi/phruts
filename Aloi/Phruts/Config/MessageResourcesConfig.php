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
 * A PHPBean representing the configuration information of
 * a <message-resources> element in a PHruts configuration file.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Config_MessageResourcesConfig {
	/**
	 * Has this component been completely configured?
	 * 
	 * @var boolean
	 */
	protected $configured = false;

	/**
	 * Freeze the configuration of this component.
	 */
	public function freeze() {
		$this->configured = true;
	}

	/**
	 * Fully qualified PHP class name of the MessageResourcesFactory class
	 * we should use.
	 *
	 * @var string
	 */
	protected $factory = 'Aloi_Phruts_Util_PropertyMessageResourcesFactory';

	/**
	 * @return string
	 */
	public function getFactory() {
		return $this->factory;
	}

	/**
	 * @param string $factory
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setFactory($factory) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->factory = (string) $factory;
	}

	/**
	 * Parameter that is passed to the createResources method of our
	 * MessageResourcesFactory implementation.
	 *
	 * @var string
	 */
	protected $parameter = null;

	/**
	 * @return string
	 */
	public function getParameter() {
		return $this->parameter;
	}

	/**
	 * @param string $parameter
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setParameter($parameter) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->parameter = (string) $parameter;
	}

	/**
	 * The servlet context attributes key under which this MessageResources
	 * instance is stored.
	 * 
	 * @var string
	 */
	protected $key = Aloi_Phruts_Globals :: MESSAGES_KEY;

	/**
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @param string $key
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setKey($key) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->key = (string) $key;
	}

	/**
	 * Should we return null for unknown message keys?
	 * 
	 * @var boolean
	 */
	protected $nullValue = true;

	/**
	 * @return boolean
	 */
	public function getNull() {
		return $this->nullValue;
	}

	/**
	 * @param boolean $nullValue
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setNull($nullValue) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$temp = strtolower($nullValue);
		if ($temp === 'false' || $temp === 'no') {
			$this->nullValue = false;
		} else {
			$this->nullValue = (boolean) $nullValue;
		}
	}

	/**
	 * Return a String representation of this object.
	 * 
	 * @return string
	 */
	public function __toString() {
		$sb = 'Aloi_Phruts_Config_MessageResourcesConfig[';
		$sb .= 'key=' . var_export($this->key, true);
		$sb .= ',factory=' . var_export($this->factory, true);
		$sb .= ',parameter=' . var_export($this->parameter, true);
		$sb .= ',null=' . var_export($this->nullValue, true);
		$sb .= ']';
		return $sb;
	}
}
?>
