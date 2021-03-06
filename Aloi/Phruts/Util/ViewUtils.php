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
 * permission notice:
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

class Aloi_Phruts_Util_ViewUtils {
	// Standard serphlet accessors
	public static function getRequest() {
		return Aloi_Serphlet_Host::getRequest();
	}
	public static function getResponse() {
		return Aloi_Serphlet_Host::getResponse();
	}
	public static function getSession() {
		return self::getRequest()->getSession(true);
	}
	public static function getServlet() {
		return Aloi_Serphlet_Host::getServlet();
	}

	/**
	 * Create (if necessary) and return a ActionForm instance.
	 *
	 * @param string $name Name of the request scope or session scope form bean
	 * (as defined by the scope attribute). If no such bean is found, a new bean
	 * will be created and added to the appropriate scope, using the PHP class
	 * name specified by the type attribute.
	 * @param string $type Fully qualified PHP class name of the form bean to be
	 * created, if no such bean is found in the specified scope.
	 * @param string $scope Scope within which the form bean will be accessed or
	 * created (must be either request or session).
	 * @return ActionForm
	 * @todo Add action parameter which indicate the URL to which this form will
	 * be submitted, used to select the ActionConfig we are assumed to be
	 * processing, from which we can identify the appropriate form bean and scope.
	 */
	public static function getFormBean($name, $type, $scope = 'request', $populate = false) {
				// Look up any existing form bean instance
		if ($scope == 'request') {
			$form = self::getRequest()->getAttribute($name);
		} else {
			Aloi_Serphlet_ClassLoader::loadClass($type);
			$form = self::getRequest()->getSession()->getAttribute($name);
		}
		

		// Can we recycle the existing form bean instance (if there is one)?
		if (!is_null($form)) {
			$formClass = get_class($form);
			if (Aloi_Serphlet_ClassLoader::classIsAssignableFrom($type, $formClass)) {
				return $form;
			}
		}
		
		// Create and return a new form bean instance
		$form = Aloi_Serphlet_ClassLoader::newInstance($type, 'Aloi_Phruts_Action_Form');
		$mapping = self::getRequest()->getAttribute(Aloi_Phruts_Globals::MAPPING_KEY);
		if($mapping != null && $populate) {
			$form->setServlet(self::getServlet());
			$form->reset($mapping, self::getRequest());
	
			try {
				Aloi_Phruts_Util_RequestUtils::populate($form, $mapping->getPrefix(), $mapping->getSuffix(), self::getRequest());
			} catch (Aloi_Serphlet_Exception $e) {
				throw $e;
			}
	
			// Set the cancellation request attribute if appropriate
			if (!is_null(self::getRequest()->getParameter(Aloi_Phruts_Globals::CANCEL_PROPERTY))) {
				self::getRequest()->setAttribute(Aloi_Phruts_Globals::CANCEL_KEY, true);
			}
		}
		
		// Assign to scope
		if ($scope == 'request') {
			self::getRequest()->setAttribute($name, $form);
		} else {
			$session = self::getRequest()->getSession();
			$session->setAttribute($name, $form);
		}
		return $form;
	}
	

	/**
	 * Return the exception from the request.
	 *
	 * @param string $property the property to locate the exception.
	 * @param string $scope Scope within which the exception will be accessed
	 * (must be either request or session).
	 * @return Exception
	 */
	public static function getException($property = null) {
		// Retrieve the exception
		if($property == null) {
			$property = Aloi_Phruts_Globals::EXCEPTION_KEY;
		}
		return self::getRequest()->getAttribute($property);
	}
	
	

	/**
	 * Retrieves an internationalized message for the specified locale, using
	 * the specified message key, and write it to the output stream.
	 *
	 * Up to five parametric replacements (such as "{0}") may be specified.
	 *
	 * @param string $key The message key of the requested message, which must
	 * have a corresponding value in the message resources.
	 * @param string $bundle The name of the application scope bean under which
	 * the MessageResources object containing our messages is stored.
	 * @param string $locale The name of the session scope bean under which our
	 * currently selected Locale object is stored.
	 * @param string $arg0 First parametric replacement value, if any.
	 * @param string $arg1 Second parametric replacement value, if any.
	 * @param string $arg2 Third parametric replacement value, if any.
	 * @param string $arg3 Fourth parametric replacement value, if any.
	 */
	public static function message($key, $bundle = null, $locale = null, $arg0 = null, $arg1 = null, $arg2 = null, $arg3 = null) {

		// Retrieve message resources
		$resources = Aloi_Phruts_Util_RequestUtils::retrieveMessageResources(self::getRequest(), self::getServlet()->getServletContext(), $bundle);

		// Retrieve user locale
		$userLocale = Aloi_Phruts_Util_RequestUtils::retrieveUserLocale(self::getRequest(), $locale);
		$message = '';
		if(!empty($resoures)) $message = $resources->getMessage($userLocale, $key, $arg0, $arg1, $arg2, $arg3);
		echo $message;
	}

	/**
	 * Displays a set of error messages prepared by a business logic component
	 * and stored as a ActionErrors object, a String in request scope.
	 *
	 * <p>If such a bean is not found, nothing will be rendered.</p>
	 * <p>In order to use this tag successfully, you must have defined an
	 * application scope MessageResources bean under the default attribute name,
	 * with optional definitions of the following message keys:</p>
	 * <ul>
	 * <li><b>errors.header</b> - Text that will be rendered before the error
	 * messages list. Typically, this message text will end with <ul> to start
	 * the error messages list.</li>
	 * <li><b>errors.footer</b> - Text that will be rendered after the error
	 * messages list. Typically, this message text will begin with </ul> to end
	 * the error messages list.</li>
	 * <li><b>errors.prefix</b> - Text that will be rendered before each
	 * individual error in the list.</li>
	 * <li><b>errors.suffix</b> - Text that will be rendered after each
	 * individual error in the list.</li>
	 * </ul>
	 *
	 * @param string $property Name of the property for which error messages
	 * should be displayed. If not specified, all error messages (regardless of
	 * property) are displayed.
	 * @param string $bundle The service context attribute key for the
	 * MessageResources instance to use. If not specified, defaults to the
	 * application resources configured for our action service.
	 * @param string $locale The session attribute key for the Locale used to
	 * select messages to be displayed. If not specified, defaults to the PHruts
	 * standard value.
	 */
	public static function errors($property = '', $bundle = null, $locale = null) {

		$errors = self::getRequest()->getAttribute(Aloi_Phruts_Globals::ERROR_KEY);
		if (is_null($errors)) {
			return;
		}

		// Retrieve message resources
		$resources = Aloi_Phruts_Util_RequestUtils::retrieveMessageResources(self::getRequest(), self::getServlet()->getServletConfig()->getServletContext(), $bundle);

		// Retrieve user locale
		$userLocale = Aloi_Phruts_Util_RequestUtils::retrieveUserLocale(self::getRequest(), $locale);

		$headerPresent = $resources->isPresent($userLocale, 'errors.header');
		$footerPresent = $resources->isPresent($userLocale, 'errors.footer');
		$prefixPresent = $resources->isPresent($userLocale, 'errors.prefix');
		$suffixPresent = $resources->isPresent($userLocale, 'errors.suffix');

		$headerDone = false;
		$message = '';
		$reports = $errors->get($property);
		foreach ($reports as $report) {
			if (!$headerDone) {
				if ($headerPresent) {
					$message .= $resources->getMessage($userLocale, 'errors.header') . PHP_EOL;
				}
				$headerDone = true;
			}

			if ($prefixPresent) {
				$message .= $resources->getMessage($userLocale, 'errors.prefix');
			}

			$values = $report->getValues();
			$message .= $resources->getMessage($userLocale, $report->getKey(), $values[0], $values[1], $values[2], $values[3]);

			if ($suffixPresent) {
				$message .= $resources->getMessage($userLocale, 'errors.suffix');
			}

			$message .= PHP_EOL;
		}

		if ($headerDone && $footerPresent) {
			$message .= $resources->getMessage($userLocale, 'errors.footer') . PHP_EOL;
		}

		echo $message;
	}
	
	/**
	 * Displays a set of action messages prepared by a business logic component
	 * and stored as a ActionMessages object, a String in request scope.
	 *
	 * <p>If such a bean is not found, nothing will be rendered.</p>
	 * <p>In order to use this tag successfully, you must have defined an
	 * application scope MessageResources bean under the default attribute name,
	 * with optional definitions of the following message keys:</p>
	 * <ul>
	 * <li><b>messages.header</b> - Text that will be rendered before the action
	 * messages list. Typically, this message text will end with <ul> to start
	 * the action messages list.</li>
	 * <li><b>messages.footer</b> - Text that will be rendered after the action
	 * messages list. Typically, this message text will begin with </ul> to end
	 * the action messages list.</li>
	 * <li><b>messages.prefix</b> - Text that will be rendered before each
	 * individual action message in the list.</li>
	 * <li><b>messages.suffix</b> - Text that will be rendered after each
	 * individual action message in the list.</li>
	 * </ul>
	 *
	 * @param string $property Name of the property for which action messages
	 * should be displayed. If not specified, all action messages (regardless of
	 * property) are displayed.
	 * @param string $bundle The service context attribute key for the
	 * MessageResources instance to use. If not specified, defaults to the
	 * application resources configured for our action service.
	 * @param string $locale The session attribute key for the Locale used to
	 * select messages to be displayed. If not specified, defaults to the PHruts
	 * standard value.
	 */
	public static function messages($property = '', $bundle = null, $locale = null) {
		$messages = self::getRequest()->getAttribute(Aloi_Phruts_Globals::MESSAGE_KEY);
		if (is_null($messages)) {
			return;
		}

		// Retrieve message resources
		$resources = Aloi_Phruts_Util_RequestUtils::retrieveMessageResources(self::getRequest(), self::getServletConfig()->getServletContext(), $bundle);

		// Retrieve user locale
		$userLocale = Aloi_Phruts_Util_RequestUtils::retrieveUserLocale(self::getRequest(), $locale);

		$headerPresent = $resources->isPresent($userLocale, 'messages.header');
		$footerPresent = $resources->isPresent($userLocale, 'messages.footer');
		$prefixPresent = $resources->isPresent($userLocale, 'messages.prefix');
		$suffixPresent = $resources->isPresent($userLocale, 'messages.suffix');

		$headerDone = false;
		$message = '';
		$reports = $messages->get($property);
		foreach ($reports as $report) {
			if (!$headerDone) {
				if ($headerPresent) {
					$message .= $resources->getMessage($userLocale, 'messages.header') . PHP_EOL;
				}
				$headerDone = true;
			}

			if ($prefixPresent) {
				$message .= $resources->getMessage($userLocale, 'messages.prefix');
			}

			$values = $report->getValues();
			$message .= $resources->getMessage($userLocale, $report->getKey(), $values[0], $values[1], $values[2], $values[3]);

			if ($suffixPresent) {
				$message .= $resources->getMessage($userLocale, 'messages.suffix');
			}

			$message .= PHP_EOL;
		}

		if ($headerDone && $footerPresent) {
			$message .= $resources->getMessage($userLocale, 'messages.footer') . PHP_EOL;
		}

		echo $message;
	}
	
	
	/**
     * The transaction token stored in this session, if it is used.
     * @return string
     */
    public static function getToken() {
        if (self::getRequest()->getSession(false) == null) {
            return null;
        }
        return self::getRequest()->getSession()->getAttribute(Aloi_Phruts_Globals::TRANSACTION_TOKEN_KEY);
    }
    

    /**
     * Renders the reference for a HTML <base> element.
     */

    /**
     * Renders the reference for a HTML <base> element.
     */
    public static function getBaseHref($includeServerSB = false) {
        if (self::getRequest() == null)
            return null;
		
        $path = '';
        if($includeServerSB) $path = Aloi_Phruts_Util_RequestUtils::requestToServerStringBuffer(self::getRequest());
        $path .= self::getRequest()->getContextPath();
        
        if(substr($path, -1) != '/') $path .= '/';
        return $path;
        
        
    }
    
    public static function getServletMapping() {
        if (self::getServlet() == null) {
            return null;
        }

        //return $this->servlet->getAttribute(Aloi_Phruts_Globals::SERVLET_KEY);
        return self::getServlet()->getServletConfig()->getServletMapping();
    }
    
    
    /**
     * Return the form action converted into a server-relative URL.
     * @return string
     */
    public function getActionMappingURL($action = null) {
        if(empty($action)) {
        	// Get it from the request
        	if(self::getRequest() == null) return;
        	$mapping = self::getRequest()->getAttribute(Aloi_Phruts_Globals::MAPPING_KEY);
        	$action = $mapping->getPath();
        }
    	
    	// Use our servlet mapping, if one is specified
        $servletMapping = self::getServletMapping();
        if ($servletMapping == null) {
         	$servletMapping = '/*'; // Set to default
        }
        
        $moduleName = Aloi_Phruts_Util_RequestUtils::getModuleName(self::getRequest(), self::getServlet()->getServletConfig()->getServletContext());
        
        // Query incomming?
        $queryString = null;
        if (preg_match('/\?/', $action)) {
            $queryString = substr($action, strpos($action, '?') + 1);
        }
        $actionMapping = self::getActionMappingName($action);
        
        $value = preg_replace('/[\/]?\*/', $moduleName . $actionMapping, $servletMapping);
            
		if(!empty($queryString)) {
			if(preg_match('/\?/', $servletMapping)) {
				$value .= '&' . $queryString;
			} else {
				$value .= '?' . $queryString;
			}
        }
        
        // If the requiest URI has index.php/ in it, we need to have index.php
        $scriptName = basename($_SERVER['SCRIPT_NAME']);
        if(strpos(self::getRequest()->getRequestURI(), $scriptName) > -1) {
        	$value = $scriptName . $value;
        }
        
        // Don't start with the slash if the result is not relative (e.g. absolute)
        if(substr($value, 0, 1) == '/') {
        	$value = substr($value, 1);
        }

        // Return the completed value
        return ($value);

    }
    
/**
     * Return the form action converted into an action mapping path.  The
     * value of the <code>action</code> property is manipulated as follows in
     * computing the name of the requested mapping:
     * <ul>
     * <li>Any filename extension is removed (on the theory that extension
     *     mapping is being used to select the controller servlet).</li>
     * <li>If the resulting value does not start with a slash, then a
     *     slash is prepended.</li>
     * </ul>
     * @return String
     */
    public function getActionMappingName($action) {
        $value = $action;
        if(preg_match('/\?/', $action)) {
        	$question = strpos($action, "?");
	        if ($question >= 0)
	            $value = substr($value, 0, $question);
        }
        $slash = strrchr($value, "/");
        $period = strrchr($value, ".");
        if (($period >= 0) && ($period > $slash))
            $value = substr($value, 0, $period);
            
        if (substr($value, 0, 1) == "/")
            return ($value);
        else
            return ("/" . $value);
    }
    
}