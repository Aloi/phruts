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
 * TokenProcessor is responsible for handling all token related functionality.  The 
 * methods in this class are synchronized to protect token processing from multiple
 * threads.  Servlet containers are allowed to return a different HttpSession
 * object for two threads accessing the same session so it is not possible to
 * synchronize on the session.
 * 
 * @author Cameron MANDERSON <cameronmanderson@gmail.com> (Aloi Contibutor)
 * @since Struts 1.1
 * @version $Id$
 */
class Aloi_Phruts_Util_TokenProcessor {

    /**
     * Retrieves the singleton instance of this class.
     * @return TokenProcessor
     */
    public static function getInstance() {
        static $instance;
        if(empty($instance)) $instance = new Aloi_Phruts_Util_TokenProcessor();
        return $instance;
    }

//	private function __construct() {
//		
//	}

    /**
     * The timestamp used most recently to generate a token value.
     */
    private $previous;

    /**
     * Return <code>true</code> if there is a transaction token stored in
     * the user's current session, and the value submitted as a request
     * parameter with this action matches it.  Returns <code>false</code>
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
     */
    public function isTokenValid(Aloi_Serphlet_Application_HttpRequest $request, $reset = false) {

        // Retrieve the current session for this request
        $session = $request->getSession(false); // HttpSession
        if ($session == null) {
            return false;
        }

        // Retrieve the transaction token from this session, and
        // reset it if requested
        $saved = $session->getAttribute(Aloi_Phruts_Globals::TRANSACTION_TOKEN_KEY);
        if ($saved == null) {
            return false;
        }

        if ($reset) {
            $this->resetToken($request);
        }

        // Retrieve the transaction token included in this request
        $token = $request->getParameter(Aloi_Phruts_Globals::TOKEN_KEY);
        if ($token == null) {
            return false;
        }

        return ($saved == $token);
    }

    /**
     * Reset the saved transaction token in the user's session.  This
     * indicates that transactional token checking will not be needed
     * on the next request that is submitted.
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
     */
    public function resetToken(Aloi_Serphlet_Application_HttpRequest $request) {

        $session = $request->getSession(false); // HttpSession 
        if ($session == null) {
            return;
        }
        $session->removeAttribute(Aloi_Phruts_Globals::TRANSACTION_TOKEN_KEY);
    }

    /**
     * Save a new transaction token in the user's current session, creating
     * a new session if necessary.
     *
     * @param Aloi_Serphlet_Application_HttpRequest request The servlet request we are processing
     */
    public function saveToken(Aloi_Serphlet_Application_HttpRequest $request) {
        $session = $request->getSession(); //HttpSession
        $token = $this->generateToken($request);
        if ($token != null) {
            $session->setAttribute(Aloi_Phruts_Globals::TRANSACTION_TOKEN_KEY, $token);
        }
    }

    /**
     * Generate a new transaction token, to be used for enforcing a single
     * request for a particular transaction.
     * 
     * @param Aloi_Serphlet_Application_HttpRequest request The request we are processing
     * @return String
     */
    public function generateToken(Aloi_Serphlet_Application_HttpRequest $request) {
        $session = $request->getSession(); //HttpSession
        $id = $session->getId();
        $current = microtime();
        if ($current == $this->previous) {
            $current++;
        }
        $this->previous = $current;
        return md5($id . $current);
    }
}
?>