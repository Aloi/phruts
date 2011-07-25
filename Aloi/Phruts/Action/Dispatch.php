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
 * <p>An abstract <strong>Action</strong> that dispatches to a public
 * method that is named by the request parameter whose name is specified
 * by the <code>parameter</code> property of the corresponding
 * ActionMapping.  This Action is useful for developers who prefer to
 * combine many similar actions into a single Action class, in order to
 * simplify their application design.</p>
 *
 * <p>To configure the use of this action in your <code>struts-config.xml</code>
 * file, create an entry like this:</p>
 *
 * <code> &lt;action path="/saveSubscription" type="actions::DispatchAction"
 * name="subscriptionForm" scope="request" input="/subscription.php" parameter="
 * method"/&gt;
 * </code>
 *
 * <p>which will use the value of the request parameter named "method"
 * to pick the appropriate "execute" method, which must have the same
 * signature (other than method name) of the standard Action.execute
 * method.  For example, you might have the following three methods in the
 * same action:</p>
 * <ul>
 * <li>public Aloi_Phruts_Config_Action delete(ActionMapping mapping, Aloi_Phruts_Action_Form form,
 * Aloi_Serphlet_Application_HttpRequest request, Aloi_Serphlet_Application_HttpResponse response)     throws
 * Exception</li>
 * <li>public Aloi_Phruts_Config_Action insert(ActionMapping mapping, Aloi_Phruts_Action_Form form,
 * Aloi_Serphlet_Application_HttpRequest request, Aloi_Serphlet_Application_HttpResponse response)     throws
 * Exception</li>
 * <li>public Aloi_Phruts_Config_Action update(ActionMapping mapping, Aloi_Phruts_Action_Form form,
 * Aloi_Serphlet_Application_HttpRequest request, Aloi_Serphlet_Application_HttpResponse response)     throws
 * Exception</li>
 * </ul>
 * <p>and call one of the methods with a URL like this:</p>
 * <code>
 *   http://localhost/myapp/index.php?do=saveSubscription&method=update
 * </code>
 *
 * <p><strong>NOTE</strong> - All of the other mapping characteristics of
 * this action must be shared by the various handlers.  This places some
 * constraints over what types of handlers may reasonably be packaged into
 * the same <code>DispatchAction</code> subclass.</p>
 *
 * @author Cameron Manderson <cameronmanderson@gmail.com> (Aloi Contributor)
 * @author Niall Pemberton <niall.pemberton@btInternet.com>
 * @author Craig R. McClanahan
 * @author Ted Husted
 * @version $Id$
 */

class Aloi_Phruts_Action_Dispatch extends Aloi_Phruts_Action {

	// ----------------------------------------------------- Instance Variables

	// --------------------------------------------------------- Public Methods

	/**
	 * Process the specified HTTP request, and create the corresponding HTTP
	 * response (or forward to another web component that will create it).
	 * Return an <code>ForwardConfig</code> instance describing where and how
	 * control should be forwarded, or <code>null</code> if the response has
	 * already been completed.
	 *
	 * @param mapping The Aloi_Phruts_Config_Action used to select this instance
	 * @param form The optional Aloi_Phruts_Action_Form bean for this request (if any)
	 * @param request The HTTP request we are processing
	 * @param response The HTTP response we are creating
	 * @return ForwardConfig
	 * @exception Exception if an exception occurs
	 */
	public function execute(Aloi_Phruts_Config_Action $mapping, $form, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
		
		// See if this is cancelled
		if ($this->isCancelled($request)) {
            $af = $this->cancelled($mapping, $form, $request, $response);
            if ($af != null) {
                return $af;
            }
        }
		
		// Identify the request parameter containing the method name
		$parameter = $mapping->getParameter();
		if ($parameter == null) {
			$message = $this->getServlet()->getInternal()->getMessage("dispatch.handler", $mapping->getPath());
			$log->error($message);
			throw new Aloi_Serphlet_Exception($message);
		}

		// Identify the method name to be dispatched to.
		// dispatchMethod() will call unspecified() if name is null
		$name = $request->getParameter($parameter);

		if($name == 'perform' || $name == 'execute') {
			$message = $this->getServlet()->getInternal()->getMessage("dispatch.recursive", $mapping->getPath());
			$log->error($message);
			throw new Aloi_Serphlet_Exception($message);
		}

		// Invoke the named method, and return the result
		return $this->dispatchMethod($mapping, $form, $request, $response, $name);
	}

	/**
	 * Method which is dispatched to when there is no value for specified
	 * request parameter included in the request.  Subclasses of
	 * <code>DispatchAction</code> should override this method if they wish
	 * to provide default behavior different than producing an HTTP
	 * "Bad Request" error.
	 *
	 */
	protected function unspecified(Aloi_Phruts_Config_Action $mapping, $form, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		$message = $this->getServlet()->getInternal()->getMessage("dispatch.parameter", $mapping->getPath(), $mapping->getParameter());
		$log = Aloi_Util_Logger_Manager::getRootLogger();
		$log->error($message);
		$response->sendError(Aloi_Serphlet_Application_HttpResponse :: SC_BAD_REQUEST, $message);
		return (null);
	}

	// ----------------------------------------------------- Protected Methods

	/**
	 * Dispatch to the specified method.
	 * @return Aloi_Phruts_Config_Action
	 */
	protected function dispatchMethod(Aloi_Phruts_Config_Action $mapping, $form, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response, $name) {
		$log = Aloi_Util_Logger_Manager::getRootLogger();

		// Make sure we have a valid method name to call.
		// This may be null if the user hacks the query string.
		if ($name == null) {
			return $this->unspecified($mapping, $form, $request, $response);
		}

		// Identify the method object to be dispatched to
		$reflectionClass = new ReflectionClass(get_class($this));
		$method = $reflectionClass->getMethod($name);
		if(empty($method) || !$method->isPublic()) {
			$message = $this->getServlet()->getInternal()->getMessage("dispatch.method", $mapping->getPath(), $name);
			$log->error($message);
			$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_INTERNAL_SERVER_ERROR, $message);
			return (null);
		}

		// Invoke the method
		$forward = call_user_func(array($this, $name), $mapping, $form, $request, $response);

		// Return the returned ActionForward instance
		return ($forward);
	}
	
	/**
     * Method which is dispatched to when the request is a cancel button submit.
     * Subclasses of <code>DispatchAction</code> should override this method if
     * they wish to provide default behavior different than returning null.
     * @since Struts 1.2.0
     */
    protected function cancelled(ActionMapping $mapping, $form, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
        return null;
    }
}