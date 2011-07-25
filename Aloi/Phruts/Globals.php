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
 * Global manifest constants for the entire PHruts framework.
 *
 * @author Cameron MANDERSON <cameronmanderson@gmail.com> (Aloi contributor)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Globals {
	/**
	 * @todo Comment the constant.
	 */
	const SESSION_GC = 'Aloi_Phruts_Action_SESSION_GS_LASTTIME';

	/**
     * The session attributes key under which our transaction token is
     * stored, if it is used.
     */
	const TRANSACTION_TOKEN_KEY = 'Aloi_Phruts_Action_TOKEN';
	const TOKEN_KEY = 'Aloi_Phruts_Action_TOKEN_KEY';

	/**
	 * The property under which a Cancel button press is reported.
	 */
	const CANCEL_PROPERTY = 'Aloi_Phruts_Action_CANCEL';

	/**
	 * The context attributes key under which our ActionServlet instance will
	 * be stored.
	 */
	const ACTION_SERVLET_KEY = 'Aloi_Phruts_Action_ACTION_SERVLET';

	/**
	 * The base of the context attributes key under which our module
	 * MessageResources will be stored.
	 *
	 * For each request processed by the controller, the MessageResources object
	 * for the module selected by the request URI currently being processed will
	 * also be exposed under this key as a request attribute.
	 */
	const MESSAGES_KEY = 'Aloi_Phruts_Action_MESSAGES';

	/**
	 * The base of the context attribute key under which our ModuleConfig
	 * data structure will be stored.
	 *
	 * This will be suffixed with the actual module prefix (including the
	 * leading "/" character) to form the actual attributes key.
	 * For each request processed by the controller servlet, the ModuleConfig
	 * object for the module selected by the request URI currently being
	 * processed will also be exposed under this key as a request attribute.
	 *
	 */
	const MODULE_KEY = 'Aloi_Phruts_Action_MODULE';

	/**
	 * The base of the context attributes key under which an array of PlugIn
	 * instances will be stored.
	 *
	 * This will be suffixed with the actual module prefix (including the leading
	 * "/" character) to form the actual attributes key.
	 */
	const PLUG_INS_KEY = 'Aloi_Phruts_Action_PLUG_INS';

	/**
	 * The context attribute under which we store our prefixes list.
	 */
	const PREFIXES_KEY = 'Aloi_Phruts_Action_PREFIXES';

	/**
	 * The base of the context attributes key under which our RequestProcessor
	 * instance will be stored.
	 *
	 * This will be suffixed with the actual module prefix (including the leading
	 * "/" character) to form the actual attributes key.
	 */
	const REQUEST_PROCESSOR_KEY = 'Aloi_Phruts_Action_REQUEST_PROCESSOR';

	/**
	 * The session attributes key under which the user's selected Locale is
	 * stored, if any.
	 *
	 * If no such attribute is found, the system default locale will be used
	 * when retrieving internationalized messages. If used, this attribute is
	 * typically set during user login processing.
	 */
	const LOCALE_KEY = 'Aloi_Phruts_Action_LOCALE';

	/**
	 * The request attributes key under which our Aloi_Phruts_Config_Action instance is passed.
	 */
	const MAPPING_KEY = 'Aloi_Phruts_Action_MAPPING_INSTANCE';

	/**
	 * The request attributes key under which a boolean true value should be
	 * stored if this request was cancelled.
	 */
	const CANCEL_KEY = 'Aloi_Phruts_Action_CANCEL';

	/**
	 * The request attributes key under which your action should store an
	 * Aloi_Phruts_Action_Errors object.
	 */
	const ERROR_KEY = 'Aloi_Phruts_Action_ERROR';

	/**
     * The request attributes key under which your action should store an
     * <code>Aloi_Phruts_Action_Aloi_Phruts_Action_Messages</code> object, if you are using the
     * corresponding custom tag library elements.
     *
     * @since Struts 1.1
     */
    const MESSAGE_KEY = 'Aloi_Phruts_Action_MESSAGE';

	/**
	 * The context attribute key under which our default configured data source
	 * is stored, if one is configured for this module.
	 */
	const DATA_SOURCE_KEY = 'Aloi_Phruts_Action_DATA_SOURCE';
	
	/**
	 * The request attributes key under which phruts custom tags might store a
	 * Throwable that caused them to report an exception at runtime. This value
	 * can be used on an error page to provide more detailed information about
	 * what realy went wrong
	 */
	const EXCEPTION_KEY = 'Aloi_Phruts_Action_EXCEPTION';
	
	/**
	 * A generic attribute key for referencing form beans
	 */
	const FORM_BEAN = 'Aloi_Phruts_Action_FORM_BEAN';
}
?>