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

/** Standard Extensions */
class Action extends Aloi_Phruts_Action {
	/**
	 * PHPMVC Refers to the servlet as 'actionServer' (alias)
	 */
	public function setServlet($servlet) {
		$this->servlet = $servlet;
		$this->actionServer = $this->servlet;
	}
	public function setActionServer($actionServer) {
		$this->setServlet($actionServer);
	}
	
	/**
	 * PHPMVC Also saves a single spot for the form bean
	 */
	public function saveFormBean($request, $formBean) {
		if(!empty($formBean)) {
			$request->setAttribute(Globals::FORM_BEAN, $formBean);
		} else {
			$request->remoteAttribute(Globals::FORM_BEAN);
		}
	}
}
class ActionForm extends Aloi_Phruts_Action_Form {
	/**
	 * PHPMVC Refers to the servlet as 'actionServer' (alias)
	 */
	private $actionServer;
	public function setServlet($servlet) {
		$this->servlet = $servlet;
		$this->actionServer = $servlet;
	}
	public function setActionServer($actionServer) {
		$this->setServlet($actionServer);
	}
	public function getActionServer() {
		return $this->getServlet();
	}
}
class ActionMessage extends Aloi_Phruts_Action_Message {}
class ActionMessages extends Aloi_Phruts_Action_Messages {}
class DispatchAction extends Aloi_Phruts_Action_Dispatch {}
class ForwardAction extends Aloi_Phruts_Action_Forward {}
class LookupDispatchAction extends Aloi_Phruts_Action_Dispatch_Lookup {}
class ActionError extends ActionMessage {}
class ActionErrors extends ActionMessages {}

class ForwardConfig extends Aloi_Phruts_Config_ForwardConfig {}
class AppServerConfig extends Aloi_Serphlet_Config_ApplicationConfig {}
class AppServerContext extends Aloi_Serphlet_Config_ApplicationContext {}
class ActionConfig extends Aloi_Phruts_Config_Action {}
class ApplicationConfig extends Aloi_Phruts_Config_ModuleConfig {}
class ControllerConfig extends Aloi_Phruts_Config_ControllerConfig {}
class DataSourceConfig extends Aloi_Phruts_Config_DataSourceConfig {}
class FormBeanConfig extends Aloi_Phruts_Config_FormBeanConfig {}
class MessageResourcesConfig extends Aloi_Phruts_Config_MessageResourcesConfig {}
class ActionForward extends ForwardConfig {}

abstract class MessageResources extends Aloi_Phruts_Util_MessageResources {}
abstract class MessageResourcesFactory extends Aloi_Phruts_Util_MessageResourcesFactory {}
class PhpBeanUtils extends Aloi_Phruts_Util_BeanUtils {}
class PropertyMessageResources extends Aloi_Phruts_Util_PropertyMessageResources {}
class PropertyMessageResourcesFactory extends Aloi_Phruts_Util_PropertyMessageResourcesFactory {}
class RequestUtils extends Aloi_Phruts_Util_RequestUtils {}
class Locale extends Aloi_Util_Locale {}

/**
 * Implement a mix of PlugIn
 */
abstract class APlugIn {
	protected $init = false;
	public function init($config = '') {
		if($this->init == true) return;
		$this->init = true;
	}
}

/**
 * Create a wrapper supporting both plug-in styles
 */
class Aloi_Phruts_Config_PHPMVC_PlugInWrapper implements Aloi_Phruts_PlugIn {
	private $plugIn;
	public function __construct($plugIn) {
		$className = get_class($plugIn);
		$class = new ReflectionClass($className);
		if ((!in_array($className, array('Aloi_Phruts_PlugIn', 'APlugIn'))) && !$class->isSubclassOf('Aloi_Phruts_PlugIn') && !$class->isSubclassOf('APlugIn')) {
			throw new Aloi_Serphlet_Exception_Instantiation('"' . get_class($plugIn) . '" is not a subclass of "APlugIn" or "Aloi_Phruts_PlugIn".');
		}
		$this->plugIn = $plugIn;
	}
	
	public function init(Aloi_Phruts_Action_Servlet $servlet, Aloi_Phruts_Config_ModuleConfig $config) {
		// Normal Phruts behaviour initialises on boot
		if($this->plugIn instanceof Aloi_Phruts_PlugIn) {
			$this->plugIn->init($servlet, $config);
		}
	}

	public function initAccess(Aloi_Phruts_Action_Servlet $servlet, Aloi_Phruts_Config_ModuleConfig $config) {
		// PHPMVC calls init for the first time at point of access
		if($this->plugIn instanceof APlugIn) {
			$this->plugIn->init();
		}
	}
	
	public function destroy() {
		if(method_exists($this->plugIn, 'destroy')) {
			$this->plugIn->destroy();
		}
	}
	
	public function getPlugIn() {
		return $this->plugIn;
	}
}

/** Extend wrapper functionality for backwards compatibility */
class HttpAppServer extends Aloi_Serphlet_Http_Servlet {}
class RequestProcessor extends Aloi_Phruts_RequestProcessor {
	
}
class ActionDispatcher extends Aloi_Serphlet_Application_RequestDispatcher {
	
	/**
	 * PHPMVC Refers to the servlet as 'actionServer' (alias)
	 */
	private $actionServer;
	public function setServlet($servlet) {
		$this->servlet = $servlet;
		$this->actionServer = $servlet;
	}
	public function setActionServer($actionServer) {
		$this->setServlet($actionServer);
	}
	public function getActionServer() {
		return $this->getServlet();
	}
	public function getServlet() {
		return $this->servlet;
	}
}
class ActionServer extends Aloi_Phruts_Action_Servlet {
	public function __construct() {
		parent::__construct();
		// Set the default config prefix to phruts-config
		$this->configPrefix = 'phruts-config';
	}
	
	
	/**
	 * Modify the standard init module plug-ins NOT to initialise
	 * unless accessed the the getPlugIn($key) method
	 */
	protected function initModulePlugIns(Aloi_Phruts_Config_ModuleConfig $config) {
		if (self :: $log->isDebugEnabled()) {
			self :: $log->debug('Initializing module "' . $config->getPrefix() . '" plug ins');
		}

		$plugInConfigs = $config->findPlugInConfigs();
		$plugIns = array ();
		foreach ($plugInConfigs as $plugInConfig) {
			try {
				$plugIn = Aloi_Serphlet_ClassLoader :: newInstance($plugInConfig->getClassName());
				Aloi_Phruts_Util_BeanUtils :: populate($plugIn, $plugInConfig->getProperties());
				$plugIn = new Aloi_Phruts_Config_PHPMVC_PlugInWrapper($plugIn);
				$plugIn->init($this, $config);

				// Add to the collection
				$plugInKey = $plugInConfig->getKey();
				if(!empty($plugInKey)) {
					$plugIns[$plugInKey] = $plugIn;
				}
				else $plugIns[] = $plugIn;
			} catch (Exception $e) {
				$msg = $this->internal->getMessage(null, 'plugIn.init', $plugInConfig->getClassName());
				self :: $log->error($msg . ' - ' . $e->getMessage());
				throw new Aloi_Serphlet_Exception($msg);
			}
		}
		$this->getServletContext()->setAttribute(Aloi_Phruts_Globals :: PLUG_INS_KEY . $config->getPrefix(), $plugIns);
	}
	
	/**
	 * PHPMVC initialises the plug in only when called
	 */
	public function getPlugIn($key, $request = null) {
		$prefix = '';
		if(!empty($request)) {
			$prefix = Aloi_Phruts_Util_RequestUtils::getModuleName($request, $this->getServletContext());
		}
		
		$config = $this->getServletContext()->getAttribute(Aloi_Phruts_Globals :: MODULE_KEY . $prefix);
		$plugIns = $this->getServletContext()->getAttribute(Aloi_Phruts_Globals :: PLUG_INS_KEY . $config->getPrefix());
		if(!empty($plugIns[$key])) {
			$plugIns[$key]->initAccess($this, $config);
			return $plugIns[$key]->getPlugIn();
		}
	}
}

/**
 * Wrapper the response object and compose with any required functions
 * @author Cameron Manderson <cameronmanderson@gmail.com> (Aloi Contributor)
 */
class ResponseBase {
	private $response;
	public function __construct(Aloi_Serphlet_Application_HttpResponse $response) {
		$this->response = $response;
	}
	
	// Wrapper the function calls
	public function __call($method, $parameters) {
		if(method_exists($this->response, $method)) {
			// Invoke the wrapper functionality
			call_user_func(array($this->response, $method), $parameters);
		}
	}
}

/**
 * Wrapper the request object and compose with any required functions
 * @author Cameron Manderson <cameronmanderson@gmail.com> (Aloi Contributor)
 */
class RequestBase {
	private $request;
	public function __construct(Aloi_Serphlet_Application_HttpRequest $request) {
		$this->request = $request;
	}
	
	// Wrapper the function calls
	public function __call($method, $parameters) {
		if(method_exists($this->request, $method)) {
			// Invoke the wrapper functionality
			call_user_func(array($this->request, $method), $parameters);
		}
	}
}
class HttpRequestBase extends RequestBase {}
class HttpResponseBase extends ResponseBase {}

/** Standalone extensions */
class BasicDataSource {}
class BootUtils {}
class ClassPath {}
class FileUtils {}
class HelperUtils {}

/** Sections left to be built into Aloi */
class ViewResourcesConfig {}
class MessageFormat {}
class Format {}

class RealmBase {}
class Principle {}
class LoginConfig {}
class GenericPrinciple extends Principle {}
class DataSourceRealm {}
class AuthenticatorBase {}
class PhpMVC_Auth_Const {}
class ServiceAuthenticator extends AuthenticatorBase {}