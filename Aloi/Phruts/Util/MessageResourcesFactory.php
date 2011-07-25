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
 * Factory for MessagesResources instances.
 * 
 * The general usage pattern for this class is:
 * <ul>
 * <li>Call <samp>createFactory</samp> to retrieve
 * a MessageResourcesFactory instance.</li>
 * <li>Set properties as required to configure this factory instance to create
 * MessageResources instances with desired characteristics.</li>
 * <li>Call the <samp>createResources</samp> method of the factory to retrieve
 * a newly instantiated MessageResources instance.</li>
 * </ul>
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 * @todo Set log informations.
 */
abstract class Aloi_Phruts_Util_MessageResourcesFactory {
	/**
	 * The fully qualified class name to be used for
	 * MessageResourcesFactory instances.
	 *
	 * @var string
	 */
	protected static $factoryClass = 'Aloi_Phruts_Util_PropertyMessageResourcesFactory';

	/**
	 * @return string
	 */
	public static function getFactoryClass() {
		return self :: $factoryClass;
	}

	/**
	 * @param string $factoryClass
	 */
	public static function setFactoryClass($factoryClass) {
		self :: $factoryClass = (string) $factoryClass;
	}

	/**
	 * Create and return a MessageResourcesFactory instance of the
	 * appropriate class, which can be used to create customized
	 * MessageResources instances.
	 * 
	 * If no such factory can be created, return null instead.
	 * 
	 * @return MessageResourcesFactory
	 */
	public static function createFactory() {
		try {
			$factory = Aloi_Serphlet_ClassLoader :: newInstance(self :: $factoryClass, 'Aloi_Phruts_Util_MessageResourcesFactory');

			// Save dynamic class path
//			API :: addInclude(self :: $factoryClass);

			return $factory;
		} catch (Exception $e) {
			return null;
		}
	}

	/**
	 * The "return null" property value to which newly created
	 * MessageResources should be initialized.
	 *
	 * @var boolean
	 */
	protected $returnNull = true;

	/**
	 * @return boolean
	 */
	public function getReturnNull() {
		return $this->returnNull;
	}

	/**
	 * @param boolean $returnNull
	 */
	public function setReturnNull($returnNull) {
		$this->returnNull = (boolean) $returnNull;
	}

	/**
	 * Create an return a newly instansiated MessageResources.
	 * 
	 * This method must be implemented by concrete subclasses.
	 *
	 * @param string $config Configuration parameter(s) for the requested bundle
	 * @return MessageResources
	 */
	public abstract function createResources($config);
}
?>
