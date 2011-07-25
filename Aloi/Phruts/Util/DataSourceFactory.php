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
 * Factory for data source instances.
 * 
 * The general usage pattern for this class is:
 * <ul>
 * <li>Call <samp>createFactory</samp> to retrieve
 * a DataSourceFactory instance.</li>
 * <li>Call the <samp>createDataSource</samp> method of the factory to retrieve
 * a newly instantiated data source instance.</li>
 * </ul>
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
abstract class Aloi_Phruts_Util_DataSourceFactory {
	/**
	 * The fully qualified class name to be used for DataSourceFactory
	 * instances.
	 *
	 * @var string
	 */
	protected static $factoryClass = 'Aloi_Phruts_Util_PDODataSourceFactory';

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
	 * Data source configuration.
	 *
	 * @var DataSourceConfig
	 */
	protected $config = null;

	/**
	 * @return DataSourceConfig
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param DataSourceConfig $config
	 */
	public function setConfig(Aloi_Phruts_Config_DataSourceConfig $config) {
		$this->config = $config;
	}

	/**
	 * Create and return a DataSourceFactory instance of the appropriate
	 * class, which can be used to create customized data source instances.
	 * 
	 * @param DataSourceConfig $config
	 * @return DataSourceFactory
	 */
	public static function createFactory(Aloi_Phruts_Config_DataSourceConfig $config) {
		try {
			$factory = Aloi_Serphlet_ClassLoader :: newInstance(self :: $factoryClass, 'Aloi_Phruts_Util_DataSourceFactory');

			// Save dynamic class path
//			API :: addInclude(self :: $factoryClass);

			$factory->setConfig($config);
			return $factory;
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Create a data source object.
	 *
	 * @return object
	 * @throws Exception
	 */
	abstract public function createDataSource();
}