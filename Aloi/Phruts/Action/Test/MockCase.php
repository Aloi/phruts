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
 * Copyright (C) 2002 Deryl Seale
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
 * A Mock Test Case for PHP Unit. Provides a mock servlet container
 * for testing out Phruts behaviour
 * @author Cameron Manderson <cameronmanderson@gmail.com> (Aloi Contributor)
 */
class Aloi_Phruts_Action_Test_MockCase extends PHPUnit_Framework_TestCase {
	protected $initialised = false;
	
	protected $request;
	protected $response;
	protected $actionServlet;
	protected $config;
	protected $context;
	
	protected function init() {
		if($this->initialised == false)
			throw new PHPUnit_Framework_AssertionFailedError('You are overwriting the setUp() method without calling parent::setUp(). Implement this properly in subclasses');
	}
	
	protected function setUp() {
		$this->initialised = true;
		$this->config = new Aloi_Serphlet_Config_ApplicationConfig();
		$this->context = new Aloi_Serphlet_Config_ApplicationContext(getcwd(), null);
		$this->config->setServletContext($this->context);
		$this->request = new Test_Serphlet_RequestWrapper();
		$this->response = new Aloi_Serphlet_Application_HttpResponse();
		if(empty($this->actionServlet)) $this->actionServlet = new Aloi_Phruts_Action_Servlet();
	}
	
	
	protected function tearDown() {
		unset($this->config);
		unset($this->context);
		unset($this->request);
		unset($this->response);
		$this->initialised = false;
	}
	
	protected function getRequest() {
		$this->init();
		return $this->request;
	}
	
	protected function clearRequestParameters() {
		$this->request->clearParameters();
	}
	
	protected function getResponse() {
		$this->init();
		return $this->response;
	}
	
	protected function getSession() {
		$this->init();
		return $this->request->getSession();
	}
	
	protected function getActionServlet() {
		$this->init();
		try {
			if($this->actionServletInitialised == false) {
				$this->actionServlet->init($this->config);
				$this->actionServletInitialised = true;
			}
		} catch(Exception $e) {
			throw new PHPUnit_Framework_AssertionFailedError('Servlet initialisation failed ' . $e->getMessage());
		}
		return $this->actionServlet;
	}
	
	protected function actionPerform() {
		$this->init();
		$request = $this->getRequest();
		$response = $this->getResponse();
		
		try {
			$this->getActionServlet()->doPost($request, $response);
		} catch(Exception $e) {
			throw new PHPUnit_Framework_AssertionFailedError('An uncaught exception occured during performing the action ' . $e->getMessage());
		}
	}
	
	protected function setRequestPathInfo($module, $path) {
		$this->getRequest()->setPathInfo($path);
	}
	
	function setConfigFile($moduleName, $pathnname) {
		$this->init();
        if ($moduleName == null)
            $this->config->setInitParameter("config", $pathname);
        else
            $this->config->setInitParameter("config/" + $moduleName, $pathname);
		$this->actionServletInitialised = false;
	}
	
	protected function setServletConfigFile($path) {
		// Process the configuration
		$this->init();
		try {
			$digester = new Aloi_Phigester_Digester();
			$digester->push($this->config);
			$digester->addCallMethod('web-app/servlet/init-param', 'setInitParameter', 2);
			$digester->addCallParam("web-app/servlet/init-param/param-name", 0);
        	$digester->addCallParam("web-app/servlet/init-param/param-value", 1);
			$fileExists = @fopen($path, 'r', true);
			if (!$fileExists) {
				throw new PHPUnit_Framework_AssertionFailedError('The path ' . $path . ' was not found');
			} else fclose($fileExists);
        	$digester->parse($path);
			unset ($digester);
		} catch(Exception $e) {
			throw new PHPUnit_Framework_AssertionFailedError('Received exception while processing the web-app xml ' . $e->getMessage());
		}
		
		try {
			$digester = new Aloi_Phigester_Digester();
			$digester->push($this->context);
			$digester->addCallMethod('web-app/context-param', 'setInitParameter', 2);
			$digester->addCallParam("web-app/context-param/param-name", 0);
        	$digester->addCallParam("web-app/context-param/param-value", 1);
			$fileExists = @fopen($path, 'r', true);
			if (!$fileExists) {
				throw new PHPUnit_Framework_AssertionFailedError('The path ' . $path . ' was not found');
			} else fclose($fileExists);
        	$digester->parse($path);
			unset ($digester);
		} catch(Exception $e) {
			throw new PHPUnit_Framework_AssertionFailedError('Received exception while processing the web-app xml ' . $e->getMessage());
		}
		$this->actionServletInitialised = false;
	}
	
	protected function addRequestParameter($parameterName, $parameterValue) {
		$this->init();
		$this->request->addParameter($parameterName, $parameterValue);
	}
	
	protected function setInitParameter($key, $value) {
		$this->init();
		$this->config->setInitParameter($key, $value);
		$this->actionServletInitialised = false;
	}
	
	protected function getActualForward() {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
	
	protected function verifyNoActionMessages() {
		$this->init();
		$messages = $this->request->getAttribute(Aloi_Phruts_Globals::MESSAGE_KEY);
		if(!empty($messages)) {
			$messages = $messages->get();
			$messageText = array();
			foreach($messages as $message) {
				$messageText[] = $message->getKey();
			}
			throw new PHPUnit_Framework_AssertionFailedError('Was not expecting action messages, but received "' . implode('", "', $messageText) . '"');
		}
	}
	
	protected function verifyActionMessages($messageNames) {
		$this->init();
		$messages = $this->request->getAttribute(Aloi_Phruts_Globals::MESSAGE_KEY);
		if(empty($messages)) {
			throw new PHPUnit_Framework_AssertionFailedError('Was expecting ' . $messageLabel . ' messages, but found none');
		}
		
		// Check them matching
		if($messages->size() != count($messageNames)) {
			throw new PHPUnit_Framework_AssertionFailedError('Was expecting ' . count($messageNames) . ' for action messages, but found ' . $messages->size());
		} else {
			// We have the same number, so compare the two
			$messages = $messages->get();
			$messageKeys = array();
			foreach($messages as $message) $messageKeys[] = $message->getKey();
			
			$missing = array();
			foreach($messageNames as $messageName) {
				if(!in_array($messageName, $messageKeys)) {
					$missing = $messageName;
				}
			}
			if(!empty($missing)) {
				PHPUnit_Framework_AssertionFailedError('Was expecting ' . implode(', ', $messageNames) . ' for action messages, but missed ' . implode(', ', $missing) . ' messages');
			}
		}
	}
	
	protected function verifyForward($key) {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
	
	protected function verifyForwardPath($forwardPath) {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
	
	protected function verifyInputForward() {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
	
	protected function verifyActionErrors($errorNames) {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
	
	protected function verifyNoActionErrors() {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
	
	protected function getActionForm() {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
	
	protected function setActionForm($form) {
		throw new PHPUnit_Framework_AssertionFailedError('Not implemented');
	}
}