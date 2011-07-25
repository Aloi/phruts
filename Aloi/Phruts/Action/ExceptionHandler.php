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
 * An ExceptionHandler is configured in the Struts configuration file to handle
 * a specific type of exception thrown by an Action's execute method.
 * 
 * @author Cameron Manderson (Contributor from Aloi)
 * @author Original struts author unknown
 * @since Struts 1.1
 * @version $Id$
 */
class Aloi_Phruts_Action_ExceptionHandler {
    
    /**
     * Handle the exception.
     * Return the <code>ActionForward</code> instance (if any) returned by
     * the called <code>ExceptionHandler</code>.
     *
     * @param ex The exception to handle
     * @param ae The ExceptionConfig corresponding to the exception
     * @param mapping The ActionMapping we are processing
     * @param formInstance The Aloi_Phruts_Action_Form we are processing
     * @param request The servlet request we are processing
     * @param response The servlet response we are creating
     * @return ActionForward
     * @exception ServletException if a servlet exception occurs
     *
     * @since Struts 1.1
     */
    public function execute(Exception $ex,
                                 Aloi_Phruts_Config_ExceptionConfig $ae,
                                 Aloi_Phruts_Config_Action $mapping,
                                 $formInstance,
                                 Aloi_Serphlet_Application_HttpRequest $request,
                                 Aloi_Serphlet_Application_HttpResponse $response) {

        $forward = null; //ActionForward 
        $error = null; //Aloi_Phruts_Action_Error 
        $property = null; //String

        // Build the forward from the exception mapping if it exists
        // or from the form input
        if ($ae->getPath() != null) {
            $forward = new Aloi_Phruts_Config_ForwardConfig();
            $forward->setPath($ae->getPath());
        } else {
            $forward = $mapping->getInputForward();
        }

        // Figure out the error
        if ($ex instanceof Aloi_Phruts_Config_ModuleException) {
            $error = $ex->getError();
            $property = $ex->getProperty();
        } else {
            $error = new Aloi_Phruts_Action_Error($ae->getKey(), $ex->getMessage());
            $property = $error->getKey();
        }

        // Store the exception
        $request->setAttribute(Aloi_Phruts_Globals::EXCEPTION_KEY, $ex);
        $this->storeException($request, $property, $error, $forward, $ae->getScope());

        return $forward;
    }

    /**
     * Default implementation for handling an <b>Aloi_Phruts_Action_Error</b> generated
     * from an Exception during <b>Action</b> delegation.  The default
     * implementation is to set an attribute of the request or session, as
     * defined by the scope provided (the scope from the exception mapping).  An
     * <b>Aloi_Phruts_Action_Errors</b> instance is created, the error is added to the collection
     * and the collection is set under the Aloi_Phruts_Globals.ERROR_KEY.
     *
     * @param request - The request we are handling
     * @param property  - The property name to use for this error
     * @param error - The error generated from the exception mapping
     * @param forward - The forward generated from the input path (from the form or exception mapping)
     * @param scope - The scope of the exception mapping.
     */
    protected function storeException(Aloi_Serphlet_Application_HttpRequest $request,
                        $property,
                        Aloi_Phruts_Action_Error $error,
                        Aloi_Phruts_Config_ForwardConfig $forward,
                        $scope) {
                            
        $errors = new Aloi_Phruts_Action_Errors();
        $errors->add($property, $error);

        if ($scope == "request"){
            $request->setAttribute(Aloi_Phruts_Globals::ERROR_KEY, $errors);
        } else {
            $request->getSession()->setAttribute(Aloi_Phruts_Globals::ERROR_KEY, $errors);
        }
    }
}
?>