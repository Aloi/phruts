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
 * The set of Digester rules required to parse a PHruts configuration file
 * (phruts-config.xml).
 *
 * @author Cameron MANDERSON <cameronmanderson@gmail.com> (Aloi contributor)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Config_ConfigRuleSet extends Aloi_Phigester_RuleSetBase {
	private $configPrefix;
	public function __construct($configPrefix = 'phruts-config') {
		// Set the config prefix
		$this->configPrefix = $configPrefix;
	}
	
	
	/**
	 * Add the set of Rule instances defined in this RuleSet to the
	 * specified Digester instance.
	 *
	 * This method should only be called by a Digester instance. These
	 * rules assume that an instance of ModuleConfig is
	 * pushed onto the evaluation stack before parsing begins.
	 *
	 * @param Digester $digester Digester instance to which the
	 * new Rule instances should be added.
	 */
	public function addRuleInstances(Aloi_Phigester_Digester $digester) {

		$digester->addFactoryCreate($this->configPrefix . '/data-sources/data-source', new Aloi_Phruts_Config_DataSourceConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/data-sources/data-source');
		$digester->addSetNext($this->configPrefix . '/data-sources/data-source', 'addDataSourceConfig');
		$digester->addRule($this->configPrefix . '/data-sources/data-source/set-property', new Aloi_Phruts_Config_AddDataSourcePropertyRule());

		$digester->addRule($this->configPrefix . '/action-mappings', new Aloi_Phruts_Config_SetClassRule());

		$digester->addFactoryCreate($this->configPrefix . '/action-mappings/action', new Aloi_Phruts_Config_ActionFactory());
		$digester->addSetProperties($this->configPrefix . '/action-mappings/action');
		$digester->addSetNext($this->configPrefix . '/action-mappings/action', 'addActionConfig');
		$digester->addSetProperty($this->configPrefix . '/action-mappings/action/set-property', 'property', 'value');

		$digester->addFactoryCreate($this->configPrefix . '/action-mappings/action/exception', new Aloi_Phruts_Config_ExceptionConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/action-mappings/action/exception');
		$digester->addSetNext($this->configPrefix . '/action-mappings/action/exception', 'addExceptionConfig');
		$digester->addSetProperty('struts-config/action-mappings/action/exception/set-property', 'property', 'value');

		$digester->addFactoryCreate($this->configPrefix . '/action-mappings/action/forward', new Aloi_Phruts_Config_ForwardConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/action-mappings/action/forward');
		$digester->addSetNext($this->configPrefix . '/action-mappings/action/forward', 'addForwardConfig');
		$digester->addSetProperty($this->configPrefix . '/action-mappings/action/forward/set-property', 'property', 'value');

		$digester->addFactoryCreate($this->configPrefix . '/controller', new Aloi_Phruts_Config_ControllerConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/controller');
		$digester->addSetNext($this->configPrefix . '/controller', 'setControllerConfig');
		$digester->addSetProperty($this->configPrefix . '/controller/set-property', 'property', 'value');

		$digester->addFactoryCreate($this->configPrefix . '/form-beans/form-bean', new Aloi_Phruts_Config_FormBeanConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/form-beans/form-bean');
		$digester->addSetNext($this->configPrefix . '/form-beans/form-bean', 'addFormBeanConfig');
		$digester->addSetProperty($this->configPrefix . '/form-beans/form-bean/set-property', 'property', 'value');
		
		$digester->addFactoryCreate($this->configPrefix . '/form-beans/form-bean/form-property', new Aloi_Phruts_Config_FormPropertyConfigFactory());
        $digester->addSetProperties($this->configPrefix . '/form-beans/form-bean/form-property');
        $digester->addSetNext($this->configPrefix . '/form-beans/form-bean/form-property', 'addFormPropertyConfig');
        $digester->addSetProperty($this->configPrefix . '/form-beans/form-bean/form-property/set-property', 'property', 'value');
        
		
		$digester->addFactoryCreate($this->configPrefix . '/global-exceptions/exception', new Aloi_Phruts_Config_ExceptionConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/global-exceptions/exception');
		$digester->addSetNext($this->configPrefix . '/global-exceptions/exception', 'addExceptionConfig');
		$digester->addSetProperty($this->configPrefix . '/global-exceptions/exception/set-property', 'property', 'value');

		$digester->addFactoryCreate($this->configPrefix . '/global-forwards/forward', new Aloi_Phruts_Config_ForwardConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/global-forwards/forward');
		$digester->addSetNext($this->configPrefix . '/global-forwards/forward', 'addForwardConfig');
		$digester->addSetProperty($this->configPrefix . '/global-forwards/forward/set-property', 'property', 'value');

		$digester->addFactoryCreate($this->configPrefix . '/message-resources', new Aloi_Phruts_Config_MessageResourcesConfigFactory());
		$digester->addSetProperties($this->configPrefix . '/message-resources');
		$digester->addSetNext($this->configPrefix . '/message-resources', 'addMessageResourcesConfig');
		$digester->addSetProperty($this->configPrefix . '/message-resources/set-property', 'property', 'value');

		$digester->addObjectCreate($this->configPrefix . '/plug-in', 'Aloi_Phruts_Config_PlugInConfig');
		$digester->addSetProperties($this->configPrefix . '/plug-in');
		$digester->addSetNext($this->configPrefix . '/plug-in', 'addPlugInConfig');
		$digester->addRule($this->configPrefix . '/plug-in/set-property', new Aloi_Phruts_Config_PlugInSetPropertyRule());
	}
}

/**
 * Class that sets the name of the class to use when creating action config
 * instances.
 *
 * The value is set on the object on the top of the stack, which
 * must be a ModuleConfig.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_SetClassRule extends Aloi_Phigester_Rule {
	/**
	 * @param array $attributes
	 */
	public function begin(array $attributes) {
		if (array_key_exists('type', $attributes)) {
			$className = $attributes['type'];

			$mc = $this->digester->peek();
			$mc->setActionClass($className);
		}
	}

	/**
	 * @return string
	 */
	public function toString() {
		return 'SetActionClassRule[]';
	}
}

/**
 * An object creation factory which creates action config instances, taking
 * into account the default class name, which may have been specified on
 * the parent element and which is made available through the object on
 * the top of the stack, which must be a ModuleConfig.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_ActionFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$mc = $this->digester->peek();
			$className = $mc->getActionClass();
		}

		// Instantiate the new object and return it
		$actionConfig = null;
		try {
			$actionConfig = Aloi_Serphlet_ClassLoader :: newInstance($className, 'Aloi_Phruts_Config_Action');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('Aloi_Phruts_Config_ActionFactory->createObject(): ' . $e->getMessage());
		}
		return $actionConfig;
	}
}

/**
 * An object creation factory which creates forward config instances.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_ForwardConfigFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$className = 'Aloi_Phruts_Config_ForwardConfig';
		}

		// Instantiate the new object and return it
		$config = null;
		try {
			$config = Aloi_Serphlet_ClassLoader :: newInstance($className, 'Aloi_Phruts_Config_ForwardConfig');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('ForwardConfigFactory->createObject(): ' . $e->getMessage());
		}
		return $config;
	}
}

/**
 * An object creation factory which creates controller config instances.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_ControllerConfigFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$className = 'Aloi_Phruts_Config_ControllerConfig';
		}

		// Instantiate the new object and return it
		$config = null;
		try {
			$config = Aloi_Serphlet_ClassLoader :: newInstance($className, 'Aloi_Phruts_Config_ControllerConfig');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('Aloi_Phruts_Config_ControllerConfigFactory->createObject(): ' . $e->getMessage());
		}
		return $config;
	}
}



/**
 * An object creation factory which creates form property config instances.
 *
 * @author Cameorn MANDERSON <cameronmanderson@gmail.com> (Aloi Contributor)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_FormPropertyConfigFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$className = 'Aloi_Phruts_Config_FormPropertyConfig';
		}

		// Instantiate the new object and return it
		$config = null;
		try {
			$config = Aloi_Serphlet_ClassLoader::newInstance($className, 'Aloi_Phruts_Config_FormPropertyConfig');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('FormPropertyConfigFactory->createObject(): ' . $e->getMessage());
		}
		return $config;
	}
}

/**
 * An object creation factory which creates form bean config instances.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_FormBeanConfigFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$className = 'Aloi_Phruts_Config_FormBeanConfig';
		}

		// Instantiate the new object and return it
		$config = null;
		try {
			$config = Aloi_Serphlet_ClassLoader :: newInstance($className, 'Aloi_Phruts_Config_FormBeanConfig');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('Aloi_Phruts_Config_FormBeanConfigFactory->createObject(): ' . $e->getMessage());
		}
		return $config;
	}
}

/**
 * An object creation factory which creates message resources config instances.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_MessageResourcesConfigFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$className = 'Aloi_Phruts_Config_MessageResourcesConfig';
		}

		// Instantiate the new object and return it
		$config = null;
		try {
			$config = Aloi_Serphlet_ClassLoader :: newInstance($className, 'Aloi_Phruts_Config_MessageResourcesConfig');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('Aloi_Phruts_Config_MessageResourcesConfigFactory->createObject(): ' . $e->getMessage());
		}
		return $config;
	}
}

/**
 * An object creation factory which creates data source config instances.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_DataSourceConfigFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$className = 'Aloi_Phruts_Config_DataSourceConfig';
		}

		// Instantiate the new object and return it
		$config = null;
		try {
			$config = Aloi_Serphlet_ClassLoader :: newInstance($className, 'Aloi_Phruts_Config_DataSourceConfig');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('Aloi_Phruts_Config_DataSourceConfigFactory->createObject(): ' . $e->getMessage());
		}
		return $config;
	}
}

/**
 * An object creation factory which creates exception config instances.
 *
 * @author cameron MANDERSON <cameronmanderson@gmail.com> (Aloi contributor)
 * @version $Id$
 */
final class Aloi_Phruts_Config_ExceptionConfigFactory extends Aloi_Phigester_AbstractObjectCreationFactory {
	/**
	 * @param array $attributes
	 * @return object
	 */
	public function createObject(array $attributes) {
		// Identify the name of the class to instantiate
		if (array_key_exists('className', $attributes)) {
			$className = $attributes['className'];
		} else {
			$className = 'Aloi_Phruts_Config_ExceptionConfig';
		}


		// Instantiate the new object and return it
		$config = null;
		try {
			$config = Aloi_Serphlet_ClassLoader :: newInstance($className, 'Aloi_Phruts_Config_ExceptionConfig');
		} catch (Exception $e) {
			$this->digester->getLogger()->error('ExceptionConfigFactory->createObject(): ' . $e->getMessage());
		}
		return $config;
	}
}


/**
 * Class that calls addProperty for the top object on the stack, which must be
 * a DataSourceConfig.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_AddDataSourcePropertyRule extends Aloi_Phigester_Rule {
	/**
	 * @param array $attributes
	 */
	public function begin($attributes) {
		$dataSourceConfig = $this->digester->peek();
		$dataSourceConfig->addProperty($attributes['property'], $attributes['value']);
	}

	/**
	 * @return string
	 */
	public function toString() {
		return 'AddDataSourcePropertyRule[]';
	}
}

/**
 * Class that records the name and value of a configuration property to
 * be used in configuring a PlugIn instance when instantiated.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
final class Aloi_Phruts_Config_PlugInSetPropertyRule extends Aloi_Phigester_Rule {
	/**
	 * @param array $attributes
	 */
	public function begin($attributes) {
		$plugInConfig = $this->digester->peek();
		$plugInConfig->addProperty($attributes['property'], $attributes['value']);
	}

	/**
	 * @return string
	 */
	public function toString() {
		return 'PlugInSetPropertyRule[]';
	}
}
?>
