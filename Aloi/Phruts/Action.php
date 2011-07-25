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

/**
 * A Action is an adapter between the contents of an incoming HTTP
 * request and the corresponding business logic that should be executed to
 * process this request.
 * 
 * <p>The controller (ActionServlet) will select an appropriate
 * Action for each request, create an instance (if necessary), and call
 * the <samp>execute</samp> method.</p>
 * <p>When a Action instance is first created, the controller servlet
 * will call <samp>setServlet</samp> with a non-null argument to identify the
 * controller servlet instance to which this Action is attached. When
 * the controller servlet is to be shut down (or restarted), the
 * <samp>setServlet</samp> method will be called with a null argument, which
 * can be used to clean up any allocated resources in use by this
 * Action.</p>
 * 
 * @author Cameron Manderson <cameronmanderson@gmail.com>
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @todo Manage setServlet() calls with or without null argument.
 * @version $Id$
 */
class Aloi_Phruts_Action {
	/**
	 * The system default Locale.
	 * 
	 * @var Locale
	 */
	protected static $defaultLocale = null;

	/**
	 * The controller servlet to which we are attached.
	 * 
	 * @var ActionServlet
	 */
	protected $servlet = null;

	final public function __construct() {
		if (is_null(self :: $defaultLocale)) {
			Aloi_Phruts_Action :: $defaultLocale = Aloi_Util_Locale :: getDefault();
		}
	}

	public function __wakeup() {
		if (is_null(self :: $defaultLocale)) {
			Aloi_Phruts_Action :: $defaultLocale = Aloi_Util_Locale :: getDefault();
		}
	}

	/**
	 * Return the controller servlet instance to which we are attached.
	 * 
	 * @return ActionServlet
	 */
	public function getServlet() {
		return $this->servlet;
	}

	/**
	 * Set the controller servlet instance to which we are attached (if servlet
	 * is non-null), or release any allocated resources (if servlet is null).
	 *
	 * @param ActionServlet $servlet The new controller servlet, if any
	 * @todo Check if the parameter is a ActionServlet object.
	 */
	public function setServlet($servlet) {
		$this->servlet = $servlet;
	}

	/**
	 * Process the specified HTTP request, and create the corresponding HTTP
	 * response (or forward to another web component that will create it),
	 * with provision for handling exceptions thrown by the business logic.
	 *
	 * Return an ActionForward instance describing where and how control
	 * should be forwarded, or null if the response has already been completed.
	 *
	 * @param Aloi_Phruts_Config_Action $mapping The Aloi_Phruts_Config_Action used to select
	 * this instance
	 * @param Aloi_Phruts_Action_Aloi_Phruts_Action_Form $form The optional Aloi_Phruts_Action_Form bean for this
	 * request (if any)
	 * @param Aloi_Serphlet_Application_HttpRequest $request The HTTP request we are
	 * processing
	 * @param Aloi_Serphlet_Application_HttpResponse $response The HTTP response we are
	 * creating
	 * @return Aloi_Phruts_Config_ForwardConfig
	 * @throws Exception - if the application business logic throws an exception
	 */
	public function execute(Aloi_Phruts_Config_Action $mapping, $form, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		return null; // Override this method to provide functionality 
	}

	/**
	 * Return the specified data source for the current module.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param string $key The key specified in the <data-source> element for
	 * the requested data source
	 * @return object
	 * @throws Exception
	 */
	protected function getDataSource(Aloi_Serphlet_Application_HttpRequest $request, $key) {
		try {
			return $this->servlet->getDataSource($request, $key);
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Return the user's currently selected Locale.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The request we are processing
	 * @return Locale
	 */
	protected function getLocale(Aloi_Serphlet_Application_HttpRequest $request) {
		$session = $request->getSession();
		$locale = $session->getAttribute(Aloi_Phruts_Globals :: LOCALE_KEY);
		if (is_null($locale)) {
			$locale = self :: $defaultLocale;
		}
		return $locale;
	}

	/**
	 * Return the specified or default (key = "") message resources for the
	 * current module.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param string $key The key specified in the <message-resources> element
	 * for the requested bundle
	 * @return MessageResources
	 * @todo Implements the code for returning message resources for the default
	 * module ($request = null).
	 */
	protected function getResources(Aloi_Serphlet_Application_HttpRequest $request, $key = '') {
		if ($key == '') {
			return $request->getAttribute(Aloi_Phruts_Globals :: MESSAGES_KEY);
		} else {
			// Identify the current module
			$context = $this->servlet->getServletContext();
			$moduleConfig = RequestUtils :: getModuleConfig($request, $context);

			// Return the requested message resources instance
			return $context->getAttribute($key . $moduleConfig->getPrefix());
		}
	}

	/**
	 * Returns true if the current form's cancel button was pressed.
	 * 
	 * This method will check if the <samp>Aloi_Phruts_Globals::CANCEL_KEY</samp>
	 * request attribute has been set, which normally occurs if the cancel button
	 * was pressed by the user in the current request. If true, validation
	 * performed by a Aloi_Phruts_Action_Form <samp>validate</samp> method will have
	 * been skipped by the controller servlet.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @return boolean
	 */
	protected function isCancelled(Aloi_Serphlet_Application_HttpRequest $request) {
		return (!is_null($request->getAttribute(Aloi_Phruts_Globals :: CANCEL_KEY)));
	}

	/**
	 * Save the specified error messages keys into the appropriate request
	 * attribute, if any messages are required.
	 * 
	 * Otherwise, ensure that the request attribute is not created.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @param Aloi_Phruts_Action_Errors $errors Error messages object
	 * @todo Check if the second parameter is a Aloi_Phruts_Action_Errors object.
	 */
	protected function saveErrors(Aloi_Serphlet_Application_HttpRequest $request, $errors) {

		// Remove any error messages attribute if none are required
		if (is_null($errors) || $errors->isEmpty()) {
			$request->removeAttribute(Aloi_Phruts_Globals :: ERROR_KEY);
			return;
		}

		// Save the error messages we need
		$request->setAttribute(Aloi_Phruts_Globals :: ERROR_KEY, $errors);
	}
	
	/**
     * <p>Save the specified error messages keys into the appropriate session
     * attribute for use by the &lt;html:messages&gt; tag (if messages="false") 
     * or &lt;html:errors&gt;, if any error messages are required. Otherwise, 
     * ensure that the session attribute is empty.</p>
     *
     * @param HttpSession session The session to save the error messages in.
     * @param Aloi_Phruts_Action_Messages errors The error messages to save.
     * <code>null</code> or empty messages removes any existing error
     * Aloi_Phruts_Action_Messages in the session.
     *
     * @since Struts 1.2.7
     */
    protected function saveErrorsSession(HttpSession $session, $errors) {
        // Remove the error attribute if none are required
        if (($errors == null) || $errors->isEmpty()) {
            $session->removeAttribute(Aloi_Phruts_Globals::ERROR_KEY);
            return;
        }

        // Save the errors we need
        $session->setAttribute(Aloi_Phruts_Globals::ERROR_KEY, $errors);
    }

	/**
	 * Adds the specified errors keys into the appropriate request attribute
     * for use by the &lt;html:errors&gt; tag, if any messages are required.
	 * Initialize the attribute if it has not already been. Otherwise, ensure
     * that the request attribute is not set.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest request   The servlet request we are processing
	 * @param Aloi_Phruts_Action_Messages errors  Errors object
	 * @since Struts 1.2.1
	 */
	protected function addErrors(Aloi_Serphlet_Application_HttpRequest $request, $errors) {

		if ($errors == null){
			//	bad programmer! *slap*
			return;
		}

		// get any existing errors from the request, or make a new one
		$requestErrors = $request->getAttribute(Aloi_Phruts_Globals::ERROR_KEY); //Aloi_Phruts_Action_Messages
		if ($requestErrors == null){
			$requestErrors = new Aloi_Phruts_Action_Messages();
		}
		// add incoming errors
		$requestErrors->addMessages($errors);

		// if still empty, just wipe it out from the request
		if ($requestErrors->isEmpty()) {
			$request->removeAttribute(Aloi_Phruts_Globals::ERROR_KEY);
			return;
		}

		// Save the errors
		$request->setAttribute(Aloi_Phruts_Globals::ERROR_KEY, $requestErrors);
	}
	
	/**
     * <p>Save the specified messages keys into the appropriate request
     * attribute for use by the &lt;html:messages&gt; tag (if
     * messages="true" is set), if any messages are required. Otherwise,
     * ensure that the request attribute is not created.</p>
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing.
     * @param Aloi_Phruts_Action_Messages messages The messages to save. <code>null</code> or
     * empty messages removes any existing Aloi_Phruts_Action_Messages in the request.
     *
     * @since Struts 1.1
     */
    protected function saveMessages(Aloi_Serphlet_Application_HttpRequest $request, $messages) {

        // Remove any messages attribute if none are required
        if (($messages == null) || $messages->isEmpty()) {
            $request->removeAttribute(Aloi_Phruts_Globals::MESSAGE_KEY);
            return;
        }

        // Save the messages we need
        $request->setAttribute(Aloi_Phruts_Globals::MESSAGE_KEY, $messages);
    }


    /**
     * <p>Save the specified messages keys into the appropriate session
     * attribute for use by the &lt;html:messages&gt; tag (if
     * messages="true" is set), if any messages are required. Otherwise,
     * ensure that the session attribute is not created.</p>
     *
     * @param HttpSession session The session to save the messages in.
     * @param Aloi_Phruts_Action_Messages messages The messages to save. <code>null</code> or
     * empty messages removes any existing Aloi_Phruts_Action_Messages in the session.
     *
     * @since Struts 1.2
     */
    protected function saveMessagesSession(HttpSession $session, $messages) {
        // Remove any messages attribute if none are required
        if (($messages == null) || $messages->isEmpty()) {
            $session->removeAttribute(Aloi_Phruts_Globals::MESSAGE_KEY);
            return;
        }

        // Save the messages we need
        $session->setAttribute(Aloi_Phruts_Globals::MESSAGE_KEY, $messages);
    }
	
	/**
	 * Adds the specified messages keys into the appropriate request
	 * attribute for use by the &lt;html:messages&gt; tag (if
	 * messages="true" is set), if any messages are required.
	 * Initialize the attribute if it has not already been.
	 * Otherwise, ensure that the request attribute is not set.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest request   The servlet request we are processing
	 * @param Aloi_Phruts_Action_Messages messages  Messages object
	 * @since Struts 1.2.1
	 */
	protected function addMessages(Aloi_Serphlet_Application_HttpRequest $request, $messages) {

		if ($messages == null){
			//	bad programmer! *slap*
			return;
		}

		// get any existing errors from the request, or make a new one
		$requestMessages = $request->getAttribute(Aloi_Phruts_Globals::MESSAGE_KEY); //Aloi_Phruts_Action_Messages
		if ($requestMessages == null){
			$requestMessages = new Aloi_Phruts_Action_Messages();
		}
		// add incoming errors
		$requestMessages->addMessages($messages);

		// if still empty, just wipe it out from the request
		if ($requestMessages->isEmpty()) {
			$request->removeAttribute(Aloi_Phruts_Globals::MESSAGE_KEY);
			return;
		}

		// Save the errors
		$request->setAttribute(Aloi_Phruts_Globals::MESSAGE_KEY, $requestMessages);
	}
	
	/**
     * Retrieves any existing errors placed in the request by previous actions.  This method could be called instead
     * of creating a <code>new Aloi_Phruts_Action_Messages()<code> at the beginning of an <code>Action<code>
     * This will prevent saveErrors() from wiping out any existing Errors
     *
     * @return the Errors that already exist in the request, or a new Aloi_Phruts_Action_Messages object if empty.
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
     * @return Aloi_Phruts_Action_Messages
     * @since Struts 1.2.1
     */
    protected function getErrors(Aloi_Serphlet_Application_HttpRequest $request) {
        $errors = $request->getAttribute(Aloi_Phruts_Globals::ERROR_KEY); //Aloi_Phruts_Action_Messages
        if ($errors == null) {
            $errors = new Aloi_Phruts_Action_Messages();
        }
        return $errors;
    }
    
    /**
	 * Retrieves any existing messages placed in the request by previous actions.  This method could be called instead
	 * of creating a <code>new Aloi_Phruts_Action_Messages()<code> at the beginning of an <code>Action<code>
	 * This will prevent saveMessages() from wiping out any existing Messages
	 *
	 * @return the Messages that already exist in the request, or a new Aloi_Phruts_Action_Messages object if empty.
	 * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
	 * @return Aloi_Phruts_Action_Messages
     * @since Struts 1.2.1
	 */
	protected function getMessages(Aloi_Serphlet_Application_HttpRequest $request) {
		$messages = $request->getAttribute(Aloi_Phruts_Globals::MESSAGE_KEY); // Aloi_Phruts_Action_Messages
		if($messages == null) {
			$messages = new Aloi_Phruts_Action_Messages();
		}
		return $messages;
	}

	/**
	 * Set the user's currently selected Locale.
	 *
	 * @param Aloi_Serphlet_Application_HttpRequest $request The request we are processing
	 * @param Locale $locale The user's selected Locale to be set,
	 * or null to select the system's default Locale
	 * @todo Check if the second parameter is a Locale object.
	 */
	protected function setLocale(Aloi_Serphlet_Application_HttpRequest $request, $locale) {
		$session = $request->getSession();
		if (is_null($locale)) {
			$locale = self :: $defaultLocale;
		}
		$session->setAttribute(Aloi_Phruts_Globals :: LOCALE_KEY, $locale);
	}
	
	/**
     * <p>Generate a new transaction token, to be used for enforcing a single
     * request for a particular transaction.</p>
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The request we are processing
     * @return string
     */
    protected function generateToken(Aloi_Serphlet_Application_HttpRequest $request) {
        $token = TokenProcessor::getInstance(); // not application scope
        return $token->generateToken($request);
    }
    
    /**
     * <p>Return <code>true</code> if there is a transaction token stored in
     * the user's current session, and the value submitted as a request
     * parameter with this action matches it. Returns <code>false</code>
     * under any of the following circumstances:</p>
     * <ul>
     * <li>No session associated with this request</li>
     * <li>No transaction token saved in the session</li>
     * <li>No transaction token included as a request parameter</li>
     * <li>The included transaction token value does not match the
     *     transaction token in the user's session</li>
     * </ul>
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
     * @param reset Should we reset the token after checking it?
     * @return boolean
     */
    protected function isTokenValid(Aloi_Serphlet_Application_HttpRequest $request, $reset = false) {
        $token = TokenProcessor::getInstance(); // not application scope
        return $token->isTokenValid($request, $reset);
    }
    
    /**
     * <p>Reset the saved transaction token in the user's session. This
     * indicates that transactional token checking will not be needed
     * on the next request that is submitted.</p>
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
     */
    protected function resetToken(Aloi_Serphlet_Application_HttpRequest $request) {
        $token = TokenProcessor::getInstance(); // not application scope
        $token->resetToken($request);
    }
    
    /**
     * <p>Save a new transaction token in the user's current session, creating
     * a new session if necessary.</p>
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
     */
    protected function saveToken(Aloi_Serphlet_Application_HttpRequest $request) {
        $token = TokenProcessor::getInstance(); // not application scope
        $token->saveToken($request);
    }
}
?>
