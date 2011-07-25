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
 * An <strong>Aloi_Phruts_Action_Exception</strong> represents a potential exception
 * that may occur during delegation to an Action class.
 * Instances of this class may be configured in association
 * with an <code>ActionMapping</code> instance for named lookup of potentially
 * multiple destinations for a particular mapping instance.
 * <p>
 * An <code>Aloi_Phruts_Action_Exception</code> has the following minimal set of properties.
 * Additional properties can be provided as needed by subclassses.
 * 
 * <ul>
 * <li><strong>type</strong> - The fully qualified class name of the
 * exception to be associated to a particular <code>ActionMapping</code>.
 * <li><strong>key</strong>  - (Optional) Message key associated with the
 * particular exception.
 * <li><strong>path</strong> - (Optional) Context releative URI that should
 * be redirected to as a result of the exception occuring.  Will overide the
 * input form of the associated ActionMapping if one is provided.
 * <li><strong>scope</strong> - (Optional) The scope to store the exception in
 * if a problem should occur - defaults to 'request'.  Valid values are
 * 'request' and 'session'.
 * 
 * <li><strong>hierarchical</strong> - (Optional) Defines whether or not the
 * Exception hierarchy should be used when determining if an occuring
 * exception can be assigned to a mapping instance.  Default is true.
 * <li><strong>handler</strong> - (Optional) The fully qualified class name
 * of the handler, which is responsible to handle this exception.
 * Default is 'org.apache.struts.action.ExceptionHandler'.
 * </ul>
 * 
 * @author Cameron Manderson (Contributor from Aloi)
 * @author ldonlan
 * @version $Id$
 * @deprecated Replaced by org.apache.struts.config.ExceptionConfig
 */
class Aloi_Phruts_Action_Exception extends Aloi_Phruts_Config_ExceptionConfig {
	/**
	 * Returns an instance of an <code>Aloi_Phruts_Action_Error</code> configured for this
	 * exception.
	 * @return Aloi_Phruts_Action_Error
	 */
	public function getError() {
		return new Aloi_Phruts_Action_Error($this->key);
	}
}
?>