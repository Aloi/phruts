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
 * RequestProcessor contains the processing logic that the PHruts
 * controller servlet performs as it receives each servlet request.
 *
 * You can customize the request processing behavior by subclassing this
 * class and overriding the method(s) whose behavior you are
 * interested in changing.
 *
 * @author Cameron MANDERSON <cameronmanderson@gmail.com> (Contributor from
 * Aloi)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_RequestProcessor {
	
	const INCLUDE_SERVLET_PATH = 'Aloi_Serphlet_Include.servlet_path';
	const INCLUDE_PATH_INFO = 'Aloi_Serphlet_Include.path_info';
	
	/**
	 * Commons Logging instance.
	 *
	 * @var Logger
	 */
	protected static $log = null;

	/**
	 * The set of Action instances that have been created and initialized,
	 * keyed by the fully qualified PHP class name of the Action class.
	 *
	 * @var array
	 */
	protected $actions = array ();

	/**
	 * The ModuleConfiguration we are associated with.
	 *
	 * @var Aloi_Phruts_Config_ModuleConfig
	 */
	protected $moduleConfig = null;

	/**
	 * The controller servlet we are associated with.
	 *
	 * @var Aloi_Phruts_Action_Servlet
	 */
	protected $servlet = null;

	public function __wakeup() {
		if (is_null(self::$log)) {
			self::$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
		}
	}

	final public function __construct() {
		if (is_null(self::$log)) {
			self::$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
		}
	}

	/**
	 * Return the MessageResources instance containing our internal message
	 * strings.
	 *
	 * @return MessageResources
	 */
	protected function getInternal() {
		return $this->servlet->getInternal();
	}

	/**
	 * Initialize this request processor instance.
	 *
	 * @param Aloi_Phruts_Action_Servlet $servlet The Aloi_Phruts_Action_Servlet we are
	 * associated with
	 * @param Aloi_Phruts_Config_ModuleConfig $moduleConfig The Aloi_Phruts_Config_ModuleConfig we are
	 * associated with
	 * @todo Actions initializations?
	 */
	public function init(Aloi_Phruts_Action_Servlet $servlet, Aloi_Phruts_Config_ModuleConfig $moduleConfig) {
		$this->actions = array ();
		$this->servlet = $servlet;
		$this->moduleConfig = $moduleConfig;
	}

	/**
	 * Process a Aloi_Serphlet_Application_HttpRequest and create the corresponding
	 * Aloi_Serphlet_Application_HttpResponse.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The server request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The server response we are
	 * creating
	 */
	public function process(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {

		try {
			// Identify the path component we will use to select a mapping
			$path = $this->processPath($request, $response);
			if (is_null($path)) {
				return;
			}
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('Processing a "' . $request->getMethod() . '" for path "' . $path . '"');
			}

			// Select a Locale for the current user if requested
			$this->processLocale($request, $response);

			// Set the content type and no-caching headers if requested
			$this->processContent($request, $response);
			$this->processNoCache($request, $response);

			// General purpose preprocessing hook
			if (!$this->processPreprocess($request, $response)) {
				return;
			}

			//Identify the mapping for this request
			$mapping = $this->processMapping($request, $response, $path);
			if (is_null($mapping)) {
				return;
			}

			// Check for any role required to perform this action
			if (!$this->processRoles($request, $response, $mapping)) {
				return;
			}

			// Process any ActionForm bean related to this request
			$form = $this->processActionForm($request, $response, $mapping);
			$this->processPopulate($request, $response, $form, $mapping);
			if (!$this->processValidate($request, $response, $form, $mapping)) {
				return;
			}

			// Process a forward or include specified by this mapping
			if (!$this->processForward($request, $response, $mapping)) {
				return;
			}
			if (!$this->processInclude($request, $response, $mapping)) {
				return;
			}

			// Create or acquire the Action instance to process this request
			$action = $this->processActionCreate($request, $response, $mapping);
			if (is_null($action)) {
				return;
			}

			// Call the Action instance itself
			$forward = $this->processActionPerform($request, $response, $action, $form, $mapping);

			// Process the returned ActionForward instance
			$this->processForwardConfig($request, $response, $forward);
		} catch (ServletException $e) {
			throw $e;
		}
	}

	/**
	 * Identify and return the path component (from the request URI) that
	 * we will use to select a Aloi_Phruts_Config_Action to dispatch with.
	 *
	 * If no such path can be identified, create an error response
	 * and return null.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @return string
	 */
	protected function processPath(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {

        // For prefix matching, match on the path info (if any)
//        $path = (string) $request->getAttribute(self::INCLUDE_PATH_INFO);
//        if ($path == null) {
            $path = $request->getPathInfo();
//        }
//        if (($path != null) && (strlen($path) > 0)) {
//            return ($path);
//        }

        // For extension matching, strip the module prefix and extension
//        $path = (string) $request->getAttribute(self::INCLUDE_SERVLET_PATH);
//        if ($path == null) {
//            $path = $request->getServletPath();
//        }
        $prefix = $this->moduleConfig->getPrefix();
        if (substr($path, 0, strlen($prefix)) != $prefix) {
            $msg = $this->getInternal()->getMessage("processPath", $request->getRequestURI());
            self::$log->error($msg);
            $response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_BAD_REQUEST, $msg);
            return null;
        }
        
        // TODO: Add back in support for servlet path
        $path = substr($path, strlen($prefix));
        $period = strrpos($path, ".");
        if (($period >= 0) && $period !== false) {
            $path = substr($path, 0, $period);
        }
        
        return ($path);
	}

	/**
	 * Automatically select a Locale for the current user, if requested.
	 *
	 * <b>NOTE</b> - configuring Locale selection will trigger the creation
	 * of a new HttpSession if necessary.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 */
	protected function processLocale(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {

		// Are we configured to select the Locale automatically?
		if (!$this->moduleConfig->getControllerConfig()->getLocale()) {
			return;
		}

		// Has a Locale already been selected?
		$session = $request->getSession();
		if (!is_null($session->getAttribute(Aloi_Phruts_Globals::LOCALE_KEY))) {
			return;
		}

		// Use the Locale returned by the system (if any)
		$locale = $request->getLocale();
		if (!is_null($locale)) {
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  Setting user locale "' . (string) $locale . '"');
				$session->setAttribute(Aloi_Phruts_Globals::LOCALE_KEY, $locale);
			}
		}
	}

	/**
	 * Set the default content type (with optional character encoding) for
	 * all responses if requested.
	 *
	 * <b>NOTE</b> - This header will be overridden automatically if a
	 * <samp>RequestDispatcher->doForward</samp> call is ultimately
	 * invoked.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 */
	protected function processContent(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		$contentType = $this->moduleConfig->getControllerConfig()->getContentType();
		if ($contentType != '') {
			$response->setContentType($contentType);
		}
	}

	/**
	 * Ask our exception handler to handle the exception.  Return the
	 * <code>ActionForward</code> instance (if any) returned by the
	 * called <code>ExceptionHandler</code>.
	 *
	 * @param request The servlet request we are processing
	 * @param response The servlet response we are processing
	 * @param exception The exception being handled
	 * @param form The ActionForm we are processing
	 * @param mapping The ActionMapping we are using
	 *
	 * @return ActionForward
	 * @exception IOException if an input/output error occurs
	 * @exception ServletException if a servlet exception occurs
	 */
	protected function processException(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, Exception $exception, $form, Aloi_Phruts_Config_Action $mapping) {
		// Is there a defined handler for this exception?
		$config = $mapping->findExceptionConfig(get_class($exception)); // ExceptionConfig
		if ($config == null) {
			// Check the module config for a global exception
			if (self::$log->isDebugEnabled()) {
				self::$log->debug($this->getInternal()->getMessage(null, 'nonactionException', get_class($exception)));
			}
			$config = $mapping->getModuleConfig()->findExceptionConfig(get_class($exception));
		}
		
		if($config == null) {
			// There is no configuration for this exception
			if (self::$log->isDebugEnabled()) {
				self::$log->debug($this->getInternal()->getMessage(null, 'unhandledException', get_class($exception)));
			}
			// Throw the error
			throw $exception;
		}

		// Use the configured exception handling
		try {
			$handler = Aloi_Serphlet_ClassLoader::newInstance($config->getHandler(), 'Aloi_Phruts_Action_ExceptionHandler'); //ExceptionHandler
			return ($handler->execute($exception, $config, $mapping, $form, $request, $response));
		} catch (Exception $e) {
			throw new Aloi_Serphlet_Exception($e);
		}
	}

	/**
	 * Set the no-cache headers for all responses, if requested.
	 *
	 * <b>NOTE</b> - This header will be overridden automatically if a
	 * <samp>RequestDispatcher->doForward</samp> call is ultimately
	 * invoked.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 */
	protected function processNoCache(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		if ($this->moduleConfig->getControllerConfig()->getNocache()) {
			$response->setHeader('Pragma', 'No-cache');
			$response->setHeader('Cache-Control', 'no-cache');
			$response->setDateHeader('Expires', 1);
		}
	}

	/**
	 * General-purpose preprocessing hook that can be overridden as required
	 * by subclasses.
	 *
	 * Return true if you want standard processing to continue, or false if the
	 * response has already been completed. The default implementation does
	 * nothing.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @return boolean
	 */
	protected function processPreprocess(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		return true;
	}

	/**
	 * Select the mapping used to process the selection path for this request.
	 *
	 * If no mapping can be identified, create an error response and return null.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param string $path The portion of the request URI for selecting a mapping
	 * @return Aloi_Phruts_Config_Action
	 */
	protected function processMapping(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, $path) {

		// Is there a directly defined mapping for this path?
		$mapping = $this->moduleConfig->findActionConfig($path);
		if (!is_null($mapping)) {
			$request->setAttribute(Aloi_Phruts_Globals::MAPPING_KEY, $mapping);
			return $mapping;
		}

		// Locate the mapping for unknown paths (if any)
		$configs = $this->moduleConfig->findActionConfigs();
		foreach ($configs as $config) {
			if ($config->getUnknown()) {
				$request->setAttribute(Aloi_Phruts_Globals::MAPPING_KEY, $config);
				return $config;
			}
		}

		// No mapping can be found to process this request
		$msg = $this->getInternal()->getMessage(null, 'processInvalid', $path);
		self::$log->error($msg);
		$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_BAD_REQUEST, $msg);
		return null;
	}

	/**
	 * If this action is protected by security roles, make sure that the
	 * current user possesses at least one of them.
	 *
	 * Return true to continue normal processing, or false if an appropriate
	 * response has been created and processing should terminate.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param Aloi_Phruts_Config_Action $mapping The mapping we are using
	 * @return boolean
	 *
	 */
	protected function processRoles(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, Aloi_Phruts_Config_Action $mapping) {

		// Is this action protected by role requirements?
		$roles = $mapping->getRoleNames();
		if (empty ($roles)) {
			return true;
		}

		// Check the current user against the list of required roles
		foreach ($roles as $role) {
			if ($request->isUserInRole($role)) {
				if (self::$log->isDebugEnabled()) {
					self::$log->debug('  User "' . $request->getRemoteUser() . '" has role "' . $role . '", granting access');
				}
				return true;
			}
		}

		// The current user is not authorized for this action
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  User "' . $request->getRemoteUser() . '" does not have any required role, denying access');
		}
		$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_FORBIDDEN, $this->getInternal()->getMessage(null, 'notAuthorized', $mapping->getPath()));
		return false;
	}

	/**
	 * Retrieve and return the ActionForm bean associated with this
	 * mapping, creating and stashing one if necessary.
	 *
	 * If there is no form bean associated with this mapping, return null.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param Aloi_Phruts_Config_Action $mapping The mapping we are using
	 * @return ActionForm
	 */
	protected function processActionForm(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, Aloi_Phruts_Config_Action $mapping) {

		// Create (if necessary a form bean to use)
		$instance = Aloi_Phruts_Util_RequestUtils::createActionForm($request, $mapping, $this->moduleConfig, $this->servlet);
		if (is_null($instance)) {
			return null;
		}

		// Store the new instance in the appropriate scope
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Storing ActionForm bean instance in scope "' . $mapping->getScope() . '" under attribute key "' . $mapping->getAttribute() . '"');
		}
		if ($mapping->getScope() == 'request') {
			$request->setAttribute($mapping->getAttribute(), $instance);
		} else {
			$session = $request->getSession();
			$session->setAttribute($mapping->getAttribute(), $instance);
		}
		return $instance;
	}

	/**
	 * Populate the properties of the specified ActionForm instance from
	 * the request parameters included with this request.
	 *
	 * In addition, request attribute <samp>Aloi_Phruts_Globals::CANCEL_KEY</samp> will be
	 * set if the request was submitted with a cancel button.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param ActionForm $form The ActionForm instance we are
	 * populating
	 * @param Aloi_Phruts_Config_Action $mapping The ActionMapping we are using
	 * @throws ServletException - If thrown by
	 * Aloi_Phruts_Util_RequestUtils->populate()
	 */
	protected function processPopulate(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, $form, Aloi_Phruts_Config_Action $mapping) {
		if (is_null($form)) {
			return;
		}

		// Populate the bean properties of this ActionForm instance
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Populating bean properties from this request');
		}
		$form->setServlet($this->servlet);
		$form->reset($mapping, $request);

		try {
			Aloi_Phruts_Util_RequestUtils::populate($form, $mapping->getPrefix(), $mapping->getSuffix(), $request);
		} catch (ServletException $e) {
			throw $e;
		}

		// Set the cancellation request attribute if appropriate
		if (!is_null($request->getParameter(Aloi_Phruts_Globals::CANCEL_PROPERTY))) {
			$request->setAttribute(Aloi_Phruts_Globals::CANCEL_KEY, true);
		}
	}

	/**
	 * If this request was not cancelled, and the request's Aloi_Phruts_Config_Action
	 * has not disabled validation, call the validate method of the specified
	 * ActionForm, and forward back to the input form if there were any
	 * errors.
	 *
	 * Return true if we should continue processing, or false if we have already
	 * forwarded control back to the input form.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param ActionForm $form The ActionForm instance we are
	 * populating
	 * @param Aloi_Phruts_Config_Action $mapping The Aloi_Phruts_Config_Action we are using
	 * @return boolean
	 */
	protected function processValidate(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, $form, Aloi_Phruts_Config_Action $mapping) {
		if (is_null($form)) {
			return true;
		}

		// Was this request cancelled?
		if (!is_null($request->getAttribute(Aloi_Phruts_Globals::CANCEL_KEY))) {
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  Cancelled transaction, skipping validation');
			}
			return true;
		}

		// Has validation been turned off for this mapping?
		if (!$mapping->getValidate()) {
			return true;
		}

		// Call the form bean's validation method
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Validating input form properties');
		}
		$errors = $form->validate($mapping, $request);
		if (is_null($errors) || $errors->isEmpty()) {
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  No errors detected, accepting input');
			}
			return true;
		}

		// Has an input form been specified for this mapping?
		$input = $mapping->getInput();
		if (is_null($input)) {
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  Validation failed but no input form available');
			}
			$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_INTERNAL_SERVER_ERROR, $this->getInternal()->getMessage(null, 'noInput', $mapping->getPath()), $mapping->getPath());
			return false;
		}

		// Save our error messages and return to the input form if possible
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Validation failed, returning to "' . $input . '"');
		}
		$request->setAttribute(Aloi_Phruts_Globals::ERROR_KEY, $errors);

		if ($this->moduleConfig->getControllerConfig()->getInputForward()) {
			$forward = $mapping->findForward($input);
			$this->processForwardConfig($request, $response, $forward);
		} else {
			// Delegate the processing of this request
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  Delegating via forward to "' . $input . '"');
			}
			$this->doForward($input, $request, $response);
		}
	}

	/**
	 * Process a forward requested by this mapping (if any).
	 *
	 * Return true if standard processing should continue, or false if we have
	 * already handled this request.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param Aloi_Phruts_Config_Action $mapping The Aloi_Phruts_Config_Action we are using
	 * @return boolean
	 */
	protected function processForward(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, Aloi_Phruts_Config_Action $mapping) {
		// Are we going to processing this request?
		$forward = $mapping->getForward();
		if (!trim($forward)) {
			return true;
		}

		// Delegate the processing of this request
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Delegating via forward to "' . $forward . '"');
		}
		$this->doForward($forward, $request, $response);
		return false;
	}

	/**
	 * Process an include requested by this mapping (if any).
	 *
	 * Return true if standard processing should continue, or false if we have
	 * already handled this request.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param Aloi_Phruts_Config_Action $mapping The Aloi_Phruts_Config_Action we are using
	 * @return boolean
	 */
	protected function processInclude(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, Aloi_Phruts_Config_Action $mapping) {
		// Are we going to processing this request?
		$include = $mapping->getInclude();
		if (!trim($include)) {
			return true;
		}

		// Delegate the processing of this request
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Delegating via include to "' . $include . '"');
		}
		$this->doInclude($include, $request, $response);
		return false;
	}

	/**
	 * Return a Action instance that will be used to process the current
	 * request, creating a new one if necessary.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param Aloi_Phruts_Config_Action $mapping The mapping we are using
	 * @return ForwardConfig
	 */
	protected function processActionCreate(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, Aloi_Phruts_Config_Action $mapping) {

		// Acquire the Action instance we will be using (if there is one)
		$className = $mapping->getType();
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Looking for Action instance for class ' . $className);
		}

		$instance = null;

		// Return any existing Action instance of this class
		if (array_key_exists($className, $this->actions)) {
			$instance = $this->actions[$className];
		}
		if (!is_null($instance)) {
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  Returning existing Action instance');
			}
			return $instance;
		}

		// Create an return a new Action instance
		if (self::$log->isDebugEnabled()) {
			self::$log->debug('  Creating new Action instance');
		}
		try {
			$instance = Aloi_Serphlet_ClassLoader::newInstance($className, 'Aloi_Phruts_Action');

//			API::addInclude($className);
		} catch (Exception $e) {
			$msg = $this->getInternal()->getMessage(null, 'actionCreate', $mapping->getPath());
			self::$log->error($msg . ' - ' . $e->getMessage());
			$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_INTERNAL_SERVER_ERROR, $msg);
			return null;
		}

		$instance->setServlet($this->servlet);
		$this->actions[$className] = $instance;

		return $instance;
	}

	/**
	 * Ask the specified Action instance to handle this request.
	 *
	 * Return the ActionForward instance (if any) returned by the called
	 * Action for further processing.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param Action $action The Action instance to be used
	 * @param ActionForm $form The ActionForm instance to pass to
	 * this Action
	 * @param Aloi_Phruts_Config_Action $mapping The Aloi_Phruts_Config_Action instance to
	 * pass to this Action
	 * @return ForwardConfig
	 * @throws ServletException
	 */
	protected function processActionPerform(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, Aloi_Phruts_Action $action, $form, Aloi_Phruts_Config_Action $mapping) {
		try {
			return $action->execute($mapping, $form, $request, $response);
		} catch (Exception $e) {
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  Exception caught of type ' . get_class($e));
			}
			return $this->processException($request, $response, $e, $form, $mapping);
		}
	}

	/**
	 * Forward or redirect to the specified destination, by the specified
	 * mechanism.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The servlet response we are
	 * creating
	 * @param ForwardConfig $forward The ForwardConfig controlling
	 * where we go next
	 */
	protected function processForwardConfig(Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, $forward) {
		if (is_null($forward)) {
			return;
		}

		if (self::$log->isDebugEnabled()) {
			self::$log->debug('processForwardConfig(' . $forward . ')');
		}
		
		// Add back in support for calling 'nextActionPath' in the forward config
		$nextActionPath = $forward->getNextActionPath();
		if(!empty($nextActionPath)) $forwardPath = (substr($nextActionPath, 0, 1) == '/' ? '' : '/') . $nextActionPath . '.do'; // TODO: Base on current mapping
		else $forwardPath = $forward->getPath();

		if ($forward->getRedirect()) {
			// Build the forward path with a forward context relative URL
			$contextRelative = $forward->getContextRelative();
			if($contextRelative) {
				$forwardPath = $request->getContextPath() . $forwardPath;
			}
			
			$response->sendRedirect($response->encodeRedirectURL($forwardPath));
		} else {
			$this->doForward($forwardPath, $request, $response);
		}
	}

	/**
	 * Do a forward to specified uri using request dispatcher.
	 *
	 * This method is used by all internal method needing to do a forward.
	 *
	 * @param string $uri Context-relative URI to forward to
	 * @param Aloi_Serphlet_Application_HttpRequest $request Current page request
	 * @param Aloi_Serphlet_Application_HttpResponse $response Current page response
	 */
	protected function doForward($uri, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		// Identify configured action chaining
		if (preg_match('/(\/[A-z0-9]+)\.do$/', $uri, $matches)) { // TODO: Base on current servlet mapping
			if (self::$log->isDebugEnabled()) {
				self::$log->debug('  Forward identified as an action chain request');
			}
			// Set the action do path in the request and then process
			$newPath = $matches[1];
			$servletConfig = $this->servlet->getServletConfig();
			$request->setPathInfo($newPath);
			$this->process($request, $response);
			return;
		}

		$rd = $this->servlet->getServletContext()->getRequestDispatcher($uri);
		if (is_null($rd)) {
			$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_INTERNAL_SERVER_ERROR, $this->getInternal()->getMessage(null, 'requestDispatcher', $uri));
			return;
		}
		$rd->doForward($request, $response);
	}

	/**
	 * Do an include of specified uri using request dispatcher.
	 *
	 * This method is used by all internal method needing to do an include.
	 *
	 * @param string $uri Context-relative URI to include
	 * @param Aloi_Serphlet_Application_HttpRequest $request Current page request
	 * @param Aloi_Serphlet_Application_HttpResponse $response Current page response
	 */
	protected function doInclude($uri, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		$rd = $this->servlet->getServletContext()->getRequestDispatcher($uri);
		if (is_null($rd)) {
			$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_INTERNAL_SERVER_ERROR, $this->getInternal()->getMessage(null, 'requestDispatcher', $uri));
			return;
		}
		$rd->doInclude($request, $response);
	}
}
?>