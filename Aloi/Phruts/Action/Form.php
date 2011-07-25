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
 * A Aloi_Phruts_Action_Form is a PHPBean optionally associated with one or more
 * Aloi_Phruts_Config_Action.
 * 
 * <p>Such a bean will have had its properties initialized from the
 * corresponding request parameters before the corresponding action's
 * <samp>execute</samp> method is called.</p>
 * <p>When the properties of this bean have been populated, but before the
 * <samp>execute</samp> method of the action is called, this bean's
 * <samp>validate</samp> method will be called, which gives the bean a chance
 * to verify that the properties submitted by the user are correct and valid.
 * If this method finds problems, it returns an error messages object that
 * encapsulates those problems, and the controller servlet will return control
 * to the corresponding input form.  Otherwise, the <samp>validate</samp>
 * method returns null, indicating that everything is acceptable and the
 * corresponding Action's <samp>execute()</samp> method should be
 * called.</p>
 * <p>This class must be subclassed in order to be instantiated. Subclasses
 * should provide property getter and setter methods for all of the bean
 * properties they wish to expose, plus override any of the public or protected
 * methods for which they wish to provide modified functionality.</p>
 * 
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 * @todo Manage setServlet() calls with or without null argument.
 */
abstract class Aloi_Phruts_Action_Form {
	/**
	 * The controller servlet instance to which we are attached.
	 * 
	 * @var ActionServlet
	 */
	protected $servlet = null;

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
	 * Reset all bean properties to their default state.
	 * 
	 * <p>This method is called before the properties are repopulated by the
	 * controller servlet.</p>
	 * <p>The default implementation does nothing. Subclasses should override
	 * this method to reset all bean properties to default values.</p>
	 * <p>This method is <b>not</b> the appropriate place to initialize form
	 * values for an "update" type page (this should be done in a setup
	 * Action). You mainly need to worry about setting checkbox values to
	 * false; most of the time you can leave this method unimplemented.</p>
	 *
	 * @param Aloi_Phruts_Config_Action $mapping The mapping used to select this
	 * instance
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 */
	public function reset(Aloi_Phruts_Config_Action $mapping, Aloi_Serphlet_Application_HttpRequest $request) {
		// Default implementation does nothing
	}

	/**
	 * Validate the properties that have been set for this HTTP request, and
	 * return a Aloi_Phruts_Action_Errors object that encapsulates any validation errors
	 * that have been found.
	 * 
	 * <p>If no errors are found, return null or an Aloi_Phruts_Action_Errors object
	 * with no recorded error messages.</p>
	 * <p>The default implementation performs no validation and returns null.
	 * Subclasses must override this method to provide any validation they wish
	 * to perform.</p>
	 *
	 * @param Aloi_Phruts_Config_Action $mapping The mapping used to select this
	 * instance
	 * @param Aloi_Serphlet_Application_HttpRequest $request The servlet request we are
	 * processing
	 * @return Aloi_Phruts_Action_Errors
	 */
	public function validate(Aloi_Phruts_Config_Action $mapping, Aloi_Serphlet_Application_HttpRequest $request) {
		return null;
	}
}
?>
