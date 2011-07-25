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
 * General purpose utility methods related to processing a servlet request
 * in the PHruts controller framework.
 *
 * @author Cameron MANDERSON <cameronmanderson@gmail.com> (Aloi Contributor)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Util_RequestUtils {
	/**
	 * Commons Logging instance.
	 *
	 * @var Logger
	 */
	public static $log = null;

	/**
	 * Select the module to which the specified request belongs, and add
	 * corresponding request attributes to this request.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Config_ServletContext $context The Aloi_Serphlet_Config_ServletContext for this
	 * web application
	 */
	public static function selectModule(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Config_ServletContext $context) {
		// Compute module name
		$prefix = self :: getModuleName($request, $context);

		// Expose the resources for this module
		$config = $context->getAttribute(Aloi_Phruts_Globals :: MODULE_KEY . $prefix);
		if (is_null($config)) {
			$request->removeAttribute(Aloi_Phruts_Globals :: MODULE_KEY);
		} else {
			$request->setAttribute(Aloi_Phruts_Globals :: MODULE_KEY, $config);
		}
		$resources = $context->getAttribute(Aloi_Phruts_Globals :: MESSAGES_KEY . $prefix);
		if (is_null($resources)) {
			$request->removeAttribute(Aloi_Phruts_Globals :: MESSAGES_KEY);
		} else {
			$request->setAttribute(Aloi_Phruts_Globals :: MESSAGES_KEY, $resources);
		}
	}

	/**
	 * Get the module name to which the specified request belong.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param ServletContext $context The ServletContext for this
	 * web application
	 * @return string The module prefix or ""
	 */
	public static function getModuleName(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Config_ServletContext $context) {
		$path = $request->getPathInfo();
		if (self :: $log->isDebugEnabled()) {
			self :: $log->debug('Get module name for path "' . $path . '"');
		}
		
		$prefixes = $context->getAttribute(Aloi_Phruts_Globals :: PREFIXES_KEY);
		if (is_null($prefixes)) {
			$prefix = '';
		} else {
			$slashPosition = strrpos($path, '/');
			if ($slashPosition === false) {
				$prefix = '';
			} else {
				$prefix = substr($path, 0, $slashPosition);
				if (!in_array($prefix, $prefixes)) {
					$prefix = '';
				}
			}
		}
		if (self :: $log->isDebugEnabled()) {
			self :: $log->debug('Module name found: ' . (($prefix == '') ? 'default' : $prefix));
		}
		return $prefix;
	}

	/**
	 * Return the ModuleConfig object if it exists, null otherwise.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Config_ServletContext $context The Aloi_Serphlet_Config_ServletContext for this
	 * web application
	 * @return ModuleConfig The ModuleConfig object
	 */
	public static function getModuleConfig(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Config_ServletContext $context) {
		$moduleConfig = $request->getAttribute(Aloi_Phruts_Globals :: MODULE_KEY);
		if (is_null($moduleConfig)) {
			$moduleConfig = $context->getAttribute(Aloi_Phruts_Globals :: MODULE_KEY);
		}
		return $moduleConfig;
	}

	/**
	 * Create (if necessary) and return a Aloi_Phruts_Action_Form instance appropriate
	 * for this request.
	 *
	 * If no Aloi_Phruts_Action_Form instance is required, return null.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Phruts_Config_Action $mapping The action mapping for this request
	 * @param ModuleConfig $moduleConfig The configuration for this
	 * module
	 * @param ActionServlet $servlet The action servlet
	 * @return Aloi_Phruts_Action_Form Aloi_Phruts_Action_Form instance associated with this
	 * request
	 * @todo Manage exception for ClassLoader::loadClass.
	 */
	public static function createActionForm(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Phruts_Config_Action $mapping, Aloi_Phruts_Config_ModuleConfig $moduleConfig, Aloi_Phruts_Action_Servlet $servlet) {
		// Is there a form bean associated with this mapping?
		$attribute = $mapping->getAttribute();
		if (is_null($attribute)) {
			return null;
		}

		// Look up the form bean configuration information to use
		$name = $mapping->getName();
		$config = $moduleConfig->findFormBeanConfig($name);
		if (is_null($config)) {
			return null;
		}

		// Look up any existing form bean instance
		if (self :: $log->isDebugEnabled()) {
			self :: $log->debug('  Looking for Aloi_Phruts_Action_Form bean instance in scope "' . $mapping->getScope() . '" under attribute key "' . $attribute . '"');
		}
		$instance = null;
		$session = null;
		if ($mapping->getScope() == 'request') {
			$instance = $request->getAttribute($attribute);
		} else {
			Aloi_Serphlet_ClassLoader :: loadClass($config->getType());

			$session = $request->getSession();
			$instance = $session->getAttribute($attribute);
		}

		// Can we recycle the existing form bean instance (if there is one)?
		if (!is_null($instance)) {
			$configClass = $config->getType();
			$instanceClass = get_class($instance);
			if (Aloi_Serphlet_ClassLoader :: classIsAssignableFrom($configClass, $instanceClass)) {
				if (self :: $log->isDebugEnabled()) {
					self :: $log->debug('  Recycling existing Aloi_Phruts_Action_Form instance' . ' of class "' . $instanceClass . '"');
				}
				return $instance;
			}
		}

		// Create and return a new form bean instance
		try {
			$instance = Aloi_Serphlet_ClassLoader :: newInstance($config->getType(), 'Aloi_Phruts_Action_Form');
			if (self :: $log->isDebugEnabled()) {
				self :: $log->debug('  Creating new Aloi_Phruts_Action_Form instance of type "' . $config->getType() . '"');
			}
			$instance->setServlet($servlet);
		} catch (Exception $e) {
			$msg = $servlet->getInternal()->getMessage(null, 'formBean', $config->getType());
			self :: $log->error($msg . ' - ' . $e->getMessage());
		}
		return $instance;
	}

	/**
	 * Populate the properties of the specified PHPBean from the specified HTTP
	 * request, based on matching each parameter name (plus an optional prefix
	 * and/or suffix) against the corresponding JavaBeans "property setter"
	 * methods in the bean's class.
	 *
	 * If you specify a non-null prefix and non-null suffix, the parameter name
	 * must match <b>both</b> conditions for its value(s) to be used in populating
	 * bean properties.
	 *
	 * @param object $bean The PHPBean whose properties are to be set
	 * @param string $prefix The prefix (if any) to be prepend to bean property
	 * names when looking for matching parameters
	 * @param string $suffix The suffix (if any) to be appended to bean property
	 * names when looking for matching parameters
	 * @param Aloi_Serphlet_Application_HttpRequest $request The HTTP request whose parameters
	 * are to be used to populate bean properties
	 * @throws Aloi_Serphlet_Exception - If an exception is thrown while setting
	 * property values
	 */
	public static function populate($bean, $prefix, $suffix, Aloi_Serphlet_Application_HttpRequest $request) {
		$prefix = (string) $prefix;
		$suffix = (string) $suffix;
		$prefixLength = strlen($prefix);
		$suffixLength = strlen($suffix);

		// Build a list of revelant request parameters from this request
		$properties = array ();
		$names = $request->getParameterNames();
		foreach ($names as $name) {
			$stripped = $name;
			if ($prefix != '') {
				$subString = substr($stripped, 0, $prefixLength);
				if ($subString != $prefix) {
					continue;
				}
				$stripped = substr($stripped, $prefixLength);
			}
			if ($suffix != '') {
				$subString = substr($stripped, -1, $suffixLength);
				if ($subString != $suffix) {
					continue;
				}
				$stripped = substr($stripped, 0, strlen($stripped) - $suffixLength);
			}
			$properties[$stripped] = $request->getParameter($name);
		}

		// Set the corresponding properties of our bean
		try {
			Aloi_Phruts_Util_BeanUtils :: populate($bean, $properties);
		} catch (Exception $e) {
			throw new Aloi_Serphlet_Exception('Aloi_Phruts_Util_BeanUtils->populate() - ' . $e->getMessage());
		}
	}

	/**
	 * Returns the appropriate MessageResources object for the current module
	 * and the given bundle.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Config_ServletContext $context The Aloi_Serphlet_Config_ServletContext for this
	 * web application
	 * @param string $bundle The bundle name to look for. If this is null, the
	 * default bundle name is used
	 * @return MessageResources
	 * @todo If MessageResources is null throw Exception.
	 */
	public static function retrieveMessageResources(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Config_ServletContext $context, $bundle) {

		if (is_null($bundle)) {
			$bundle = Aloi_Phruts_Globals :: MESSAGES_KEY;
		} else {
			$bundle = (string) $bundle;
		}
		$resources = $request->getAttribute($bundle);

		if (is_null($resources)) {
			$config = $request->getAttribute(Aloi_Phruts_Globals :: MODULE_KEY);
			if (is_null($config)) {
				$prefix = '';
			} else {
				$prefix = $config->getPrefix();
			}
			$resources = $context->getAttribute(Aloi_Phruts_Globals :: MESSAGES_KEY . $prefix);
		}

		return $resources;
	}

	/**
	 * Look up and return current user locale, based on the specified parameters.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param string $locale Name of the session attribute for our user's
	 * Locale. If this is null, the default locale key is used for the
	 * lookup
	 * @return Locale
	 */
	public static function retrieveUserLocale(Aloi_Serphlet_Application_HttpRequest $request, $locale = null) {

		if (is_null($locale)) {
			$locale = Aloi_Phruts_Globals :: LOCALE_KEY;
		} else {
			$locale = (string) $locale;
		}
		$session = $request->getSession();
		$userLocale = $session->getAttribute($locale);

		if (is_null($userLocale)) {
			$userLocale = $request->getLocale();
		}

		return $userLocale;
	}
	
	/**
     * <p>Return <code>string</code> representing the scheme, server, and port
     * number of the current request. Server-relative URLs can be created by
     * simply appending the server-relative path (starting with '/') to this.
     * </p>
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
     *
     * @return string URL representing the scheme, server, and port number of
     * the current request
     * @since Struts 1.2.0
     */
    public static function requestToServerStringBuffer(Aloi_Serphlet_Application_HttpRequest $request) {
    	return self::createServerStringBuffer($request->getScheme(), $request->getServerName(), $request->getServerPort());
    }
    
    /**
     * <p>Return <code>StringBuffer</code> representing the scheme, server, and port number of
     * the current request.</p>
     *
     * @param scheme The scheme name to use
     * @param server The server name to use
     * @param port The port value to use
     *
     * @return string in the form scheme: server: port
     * @since Struts 1.2.0
     */
    public static function createServerStringBuffer($scheme, $server, $port) {
    	$url = '';
        if ($port < 0) {
            $port = 80;
        }
        $url .= $scheme;
        $url .= "://";
        $url .= $server;
        if (($scheme == "http" && $port != 80) || ($scheme == "https" && $port != 443)) {
            $url .= ':';
            $url .= $port;
        }
        return $url;
    }


    /**
     * <p>Return <code>string</code> representing the scheme, server, and port
     * number of the current request.</p>
     *
     * @param scheme The scheme name to use
     * @param server The server name to use
     * @param port The port value to use
     * @param uri The uri value to use
     *
     * @return StringBuffer in the form scheme: server: port
     * @since Struts 1.2.0
     */
    public static function createServerUriStringBuffer($scheme, $server, $port, $uri) {
        $serverUri = self::createServerStringBuffer($scheme, $server, $port);
        $serverUri .= $uri;
        return $serverUri;

    }
}

Aloi_Phruts_Util_RequestUtils :: $log = Aloi_Util_Logger_Manager :: getLogger('Aloi_Phruts_Util_RequestUtils');