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
 * A PHPBean representing the configuration information of a <controller>
 * element in a PHruts configuration file.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Config_ControllerConfig {
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
	 * The fully qualified class name of the RequestProcessor
	 * implementation class to be used for this module.
	 *
	 * @var string
	 */
	protected $processorClass = 'Aloi_Phruts_RequestProcessor';

	/**
	 * @return string
	 */
	public function getProcessorClass() {
		return $this->processorClass;
	}

	/**
	 * @param string $processorClass
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setProcessorClass($processorClass) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->processorClass = (string) $processorClass;
	}

	/**
	 * The content type and character encoding to be set on each response.
	 *
	 * @var string
	 */
	protected $contentType = 'text/html';

	/**
	 * @return string
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * @param string $contentType
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setContentType($contentType) {
		if (!$this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$this->contentType = (string) $contentType;
	}

	/**
	 * Should we set no-cache HTTP headers on each response?
	 *
	 * @var boolean
	 */
	protected $nocache = false;

	/**
	 * @return boolean
	 */
	public function getNocache() {
		return $this->nocache;
	}

	/**
	 * @param boolean $nocache
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setNocache($nocache) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$temp = strtolower($nocache);
		if ($temp === 'false' || $temp === 'no') {
			$this->nocache = false;
		} else {
			$this->nocache = (boolean) $nocache;
		}
	}

	/**
	 * Should the input property of Aloi_Phruts_Config_Action instances associated with
	 * this module be treated as the name of a corresponding ForwardConfig.
	 * 
	 * A false value treats them as a context-relative path.
	 *
	 * @var boolean
	 */
	protected $inputForward = false;

	/**
	 * @return boolean
	 */
	public function getInputForward() {
		return $this->inputForward;
	}

	/**
	 * @param boolean $inputForward
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setInputForward($inputForward) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$temp = strtolower($inputForward);
		if ($temp === 'false' || $temp === 'no') {
			$this->inputForward = false;
		} else {
			$this->inputForward = (boolean) $inputForward;
		}
	}

	/**
	 * Should we store a Locale object in the user's session if needed?
	 *
	 * @var boolean
	 */
	protected $locale = true;

	/**
	 * @return boolean
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @param boolean $locale
	 * @throws Aloi_Serphlet_Exception_IllegalState
	 */
	public function setLocale($locale) {
		if ($this->configured) {
			throw new Aloi_Serphlet_Exception_IllegalState('Configuration is frozen');
		}
		$temp = strtolower($locale);
		if ($temp === 'false' || $temp === 'no') {
			$this->locale = false;
		} else {
			$this->locale = (boolean) $locale;
		}
	}

	/**
	 * Return a string representation of this object.
	 *
	 * @return string
	 */
	public function __toString() {
		$sb = 'Aloi_Phruts_Config_ControllerConfig[';
		$sb .= 'processorClass=' . var_export($this->processorClass, true);
		if (!is_null($this->contentType)) {
			$sb .= ',contentType=' . var_export($this->contentType, true);
		}
		if (!is_null($this->nocache)) {
			$sb .= ',nocache=' . var_export($this->nocache, true);
		}
		if (!is_null($this->inputForward)) {
			$sb .= ',inputForward=' . var_export($this->inputForward, true);
		}
		if (!is_null($this->locale)) {
			$sb .= ',locale=' . var_export($this->locale, true);
		}
		$sb .= ']';
		return $sb;
	}
}
?>
