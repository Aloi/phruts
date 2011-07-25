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
 * Utility methods for populating PHPBeans properties via reflection.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Util_BeanUtils {
	/**
	 * Populate the PHPBeans properties of the specified bean, based on
	 * the specified name/value pairs.
	 *
	 * @param object $bean
	 * @param array $properties
	 * @throws Aloi_Serphlet_Exception_IllegalArgument - If the bean object has not been
	 * specified
	 */
	public static function populate($bean, array $properties) {
		if (is_null($bean)) {
			throw new Aloi_Serphlet_Exception_IllegalArgument('Bean object to populate must be not null.');
		}

		// Loop through the property name/value pairs to be set
		$reflection = new ReflectionClass(get_class($bean));
		foreach ($properties as $name => $value) {
			// Perform the assignement for this property
			$reflectionProperty = null;
			if (property_exists($bean, $name)) {
				$reflectionProperty = $reflection->getProperty($name);
			}
			
			if(!empty($reflectionProperty) && $reflectionProperty->isPublic()) {
				$bean->$name = $value;
			} else {
				$propertySetter = 'set' . ucfirst($name);
				if (method_exists($bean, $propertySetter)) {
					$bean->$propertySetter ($value);
				}
			}
		}
	}
}
?>
