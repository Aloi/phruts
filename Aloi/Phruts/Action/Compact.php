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
 */

/**
 * Similar to a dispatch action, the compact action allows you
 * to write more compact actions by eliminating method parameters
 * and adopting a convention of 'actionAction' in the method name
 *
 * @author Cameron Manderson <cameronmanderson@gmail.com> (Aloi Contributor)
 */
class Aloi_Phruts_Action_Compact extends Aloi_Phruts_Action {
	const ACTION_METHOD_PREPEND = 'execute';
	const ACTION_PARAMETER = 'action';
	
	// Instance Variables
	protected $request;
	protected $response;
	protected $form;
	protected $mapping;
	protected $method;
	
	public function init(Aloi_Phruts_Config_Action $mapping, $form, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		// Assign the local scope
		$this->request = $request;
		$this->response = $response;
		$this->form = $form;
		$this->mapping = $mapping;
	}
	
	// --------------------- Execute/Dispatch --------------
	public function execute(Aloi_Phruts_Config_Action $mapping, $form, Aloi_Serphlet_Application_HttpRequest $request, Aloi_Serphlet_Application_HttpResponse $response) {
		// Initialise
		$this->init($mapping, $form, $request, $response);
		
		// Log local here
		$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
		
		// Check for cancelled actions
		if($this->isCancelled($request)) {
			$log->info('Action cancelled');
			$forward = $this->cancelledAction();
			if(!empty($forward)) return $forward;
		}
		
		// Look for the name of the parameter
		$parameter = self::ACTION_PARAMETER;
		
		// Identify the action method from the request
		$this->method = $request->getParameter($parameter);
		
		// Init
		return $this->dispatchCompactMethod($this->method);
	}
	
	protected function dispatchCompactMethod($method) {
		$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
		
		// Look for the corresponding method
		if(!trim($method)) {
			return $this->executeIndex();
		}
		
		// Dispatch the method
		$localMethodName = self::ACTION_METHOD_PREPEND . ucfirst($method);
		if(!method_exists($this, $localMethodName)) {
			$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
			$message = $this->getServlet()->getInternal()->getMessage('compact.dispatchcompactmethod', $this->getMapping()->getPath(), $this->getMapping()->getParameter());
			$log->error($message);
			$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_BAD_REQUEST, $message);
			return null;
		}
		
		// Log an info
		$log->info('Dispatching method: ' . $localMethodName);
		
		// invoke
		$forward = call_user_func(array($this, $localMethodName));
		return $forward;
	}
	
	// --------------------- Default actions --------------
	public function executeIndex() {
		$log = Aloi_Util_Logger_Manager::getLogger(__CLASS__);
		$message = $this->getServlet()->getInternal()->getMessage('compact.index', $this->getMapping()->getPath(), $this->getMapping()->getParameter());
		$log->error($message);
		$response->sendError(Aloi_Serphlet_Application_HttpResponse::SC_BAD_REQUEST, $message);
	}
	public function executeCancelled() {
		return null;
	}
	
	// --------------------- Internal accessors --------------
	protected function getRequest() {
		return $this->request;
	}
	protected function getResponse() {
		return $this->response;
	}
	protected function getForm() {
		return $this->form;
	}
	protected function getMapping() {
		return $this->mapping;
	}
}