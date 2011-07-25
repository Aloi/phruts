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
 * Class used for fallback when an action config is not found. Also provides
 * Wildcard mapping support
 * @author Cameron Manderson <cameronmanderson@gmail.com> (Aloi Contributor)
 */
class Aloi_Phruts_Config_ActionMatcher {
	/** Compiled regex for mapping */
	private $regexPaths;
	
	public function __construct($configs) {
		$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
		$log->info('Looking to configs');
		$this->regexPaths = array();
		foreach($configs as $config) {
			$path = $config->getPath();
			if(strstr($path, '*') > -1) { // Look for wildcard mapping
				if(substr($path, 0, 1) == '/') $path = substr($path, 1);
				if(preg_match('/(?<!\\\\)\*/', $path)) {
					// We have possible replacements
					// TODO: Consider this replacement - additional characters? etc
					$pathAfter = preg_replace('/\*\*/', '([A-z0-9\/\_\-]+)', $path);
					$pathAfter = preg_replace('/\*/', '([A-z0-9\_\-]+)', $pathAfter);
					$pathCompiled = '/^' . $pathAfter . '$/';
					$log->info('Compiled ' . $path . ' to become ' . $pathCompiled);
					$this->regexPaths[$pathCompiled] = $config;
				}
			}
		}
 	}
 	
 	public function match($path) {
 		$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
 		$config = null;
 		if(substr($path, 0, 1) == '/') $path = substr($path, 1);
 		foreach(array_keys($this->regexPaths) as $regex) {
 			if(preg_match($regex, $path, $matches)) {
 				// We have matched the element
 				$config = $this->convertActionConfig($path, $this->regexPaths[$regex], $matches);
 				break;
 			}
 		}
 		return $config;
 	}
 	
 	protected function convertActionConfig($path, $original, $matches) {
 		$vars = array_slice($matches, 1);
 		
 		// Clone the bean and properties
 		$config = $this->cloneBean($original);
 		
 		$config->setName($this->convertParam($original->getName(), $vars));
 		if(substr($path, 0, 1) != '/') $path = '/' . $path;
 		$config->setPath($path);
 		$config->setType($this->convertParam($original->getType(), $vars));
 		$config->setRoles($this->convertParam($original->getRoles(), $vars));
 		$config->setParameter($this->convertParam($original->getParameter(), $vars));
 		$config->setAttribute($this->convertParam($original->getAttribute(), $vars));
 		$config->setForward($this->convertParam($original->getForward(), $vars));
 		$config->setInclude($this->convertParam($original->getInclude(), $vars));
 		$config->setInput($this->convertParam($original->getInput(), $vars));
 		
 		$forwardConfigs = $original->findForwardConfigs();
 		foreach($forwardConfigs as $forwardConfigOriginal) {
 			$forwardConfig = new Aloi_Phruts_Config_ForwardConfig();
 			$forwardConfig->setContextRelative($forwardConfigOriginal->getContextRelative());
 			$forwardConfig->setName($forwardConfigOriginal->getName());
 			$forwardConfig->setPath($this->convertParam($forwardConfigOriginal->getPath(), $vars));
 			$forwardConfig->setRedirect($forwardConfigOriginal->getRedirect());
 			$config->addForwardConfig($forwardConfig);
 		}
 		
 		$exceptionConfigs = $original->findExceptionConfigs();
 		foreach($exceptionConfigs as $exceptionConfig) {
 			$config->addExceptionConfig($exceptionConfig);
 		}
 		$config->freeze();
 		return $config;
 	}
 	
 	protected function convertParam($val, $matches) {
 		// Only match substitution vals
 		if(!trim($val)) return $val;
 		if(strpos($val, '{') === false) return $val;
 		
 		// Replace the instances
 		foreach($matches as $key => $replacement) {
 			$val = str_replace('{' . $key . '}', $replacement, $val);
 		}
 		return $val;
 	}
 	
 	protected function cloneBean($bean) {
 		// Convert the action config
 		$originalClass = get_class($bean);
 		$clone = new $originalClass();
 		
 		// Clone the object through public accessors
 		$reflectionClass = new ReflectionClass($originalClass);
 		$methods = $reflectionClass->getMethods();
 		foreach($methods as $method) {
 			if(substr($method->getName(), 0, 3) == 'get' && $method->isPublic()) {
 				// Check if the set exists
 				$setMethod = 's' . substr($method->getName(), 1);
 				if(method_exists($clone, $setMethod)) {
 					$setMethod = $reflectionClass->getMethod($setMethod);
 					if($setMethod->isPublic() && $setMethod->getNumberOfRequiredParameters() == 1) {
 						// Clone the property
 						$value = call_user_func(array($bean, $method->getName()));
 						if(!is_null($value)) {
 							call_user_func(array($clone, $setMethod->getName()), $value);
 						}
 					}
 				}
 			}
 		}
 		return $clone;
 	}
}