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
 * A PHPBean representing the configuration information of a <forward> element
 * from a PHruts application configuration file.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Config_ForwardConfig {
	/**
	 * Has this component been completely configured?
	 *
	 * @var boolean
	 */
	protected $configured = false;

	/**
	 * Is the redirect to be context relative
	 * @var boolean
	 */
	protected $contextRelative = false;
	
	protected $nextActionPath = null;
	
	/**
	 * Freeze the configuration of this component.
	 */
	public function freeze() {
		$this->configured = true;
	}

	/**
	 * The unique identifier of this forward, which is used to reference it
	 * in Action classes.
	 *
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
	 * Set the context relative
	 * @param string $contextRelative
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setContextRelative($contextRelative) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$temp = strtolower($contextRelative);
		if ($temp === 'false' || $temp === 'no') {
			$this->contextRelative = false;
		} else {
			$this->contextRelative = (boolean) $contextRelative;
		}
	}
	
	/**
	 * Is the forward to be context relative to the current servlet
	 * @return boolean true for context relative
	 */
	public function getContextRelative() {
		return $this->contextRelative;
	}

	/**
	 * The URL to which this ForwardConfig entry points.
	 *
	 * @var string
	 */
	protected $path = null;

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setPath($path) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->path = (string) $path;
	}

	/**
	 * Should a redirect be used to transfer control to the specified path?
	 *
	 * @var boolean
	 */
	protected $redirect = false;

	/**
	 * @return boolean
	 */
	public function getRedirect() {
		return $this->redirect;
	}

	/**
	 * @param boolean $redirect
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setRedirect($redirect) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$temp = strtolower($redirect);
		if ($temp === 'false' || $temp === 'no') {
			$this->redirect = false;
		} else {
			$this->redirect = (boolean) $redirect;
		}
	}
	
	public function getNextActionPath() {
		return $this->nextActionPath;
	}
	public function setNextActionPath($path) {
		$this->nextActionPath = $path;
	}

	/**
	 * Return a String representation of this object.
	 *
	 * @return string
	 */
	public function __toString() {
		$sb = 'Aloi_Phruts_Config_ForwardConfig[';
		$sb .= 'name=' . var_export($this->name, true);
		$sb .= ',path=' . var_export($this->path, true);
		$sb .= ',redirect=' . var_export($this->redirect, true);
		$sb .= ']';
		return $sb;
	}
}
?>
