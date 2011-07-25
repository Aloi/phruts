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
 * A PlugIn is a configuration wrapper for a module-specific resource or
 * servlet that needs to be notified about application startup and application
 * shutdown events (corresponding to calls init() and destroy() on the
 * corresponding ActionServlet instance).
 * 
 * <p>PlugIn Actions can be configured in the phruts-config.xml file, without
 * the need to subclass ActionServlet simply to perform application
 * lifecycle activities.</p>
 * <p>Implementations of this interface must supply a zero-argument constructor
 * for use by ActionServlet. Configuration can be accomplished by
 * providing standard PHPBeans property setter methods, which will all have
 * been called before the <samp>init</samp> method is invoked.</p>
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
interface Aloi_Phruts_PlugIn {
	/**
	 * Receive notification that our owning module is being shut down.
	 */
	public function destroy();

	/**
	 * Receive notification that the specified module is being started up.
	 *
	 * @param ActionServlet $servlet ActionServlet that is managing
	 * all the module in this web application
	 * @param ModuleConfig $config ModuleConfig for the module with
	 * which this plug-in is associated
	 * @throws ServletException If this PlugIn cannot be
	 * successfully initialized
	 */
	public function init(Aloi_Phruts_Action_Servlet $servlet, Aloi_Phruts_Config_ModuleConfig $config);
}
?>
