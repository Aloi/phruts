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
 * <p>A JavaBean representing the configuration information of an
 * <code>&lt;exception&gt;</code> element from a Struts
 * configuration file.</p>
 * 
 * @author Cameron Manderson (Contributor from Aloi)
 * @author Craig R. McClanahan
 * @since Struts 1.1
 * @version $Id$
 */
class Aloi_Phruts_Config_ExceptionConfig {
    /**
     * Has this component been completely configured?
     */
    protected $configured = false;

    /**
     * The servlet context attribute under which the message resources bundle
     * to be used for this exception is located.  If not set, the default
     * message resources for the current module is assumed.
     */
    protected $bundle = null;
    public function getBundle() {
        return ($this->bundle);
    }
    public function setBundle($bundle) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->bundle = $bundle;
    }


    /**
     * The fully qualified Java class name of the exception handler class
     * which should be instantiated to handle this exception.
     */
    protected $handler = "Aloi_Phruts_Action_ExceptionHandler";
    public function getHandler() {
        return ($this->handler);
    }
    public function setHandler($handler) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->handler = $handler;
    }


    /**
     * The message resources key specifying the error message
     * associated with this exception.
     */
    protected $key = null;

    public function getKey() {
        return ($this->key);
    }

    public function setKey($key) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->key = $key;
    }


    /**
     * The module-relative path of the resource to forward to if this
     * exception occurs during an <code>Action</code>.
     */
    protected $path = null;

    public function getPath() {
        return ($this->path);
    }
    public function setPath($path) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->path = $path;
    }


    /**
     * The scope in which we should expose the Aloi_Phruts_Action_Error for this exception
     * handler.
     */
    protected $scope = "request";
    public function getScope() {
        return ($this->scope);
    }
    public function setScope($scope) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->scope = $scope;
    }


    /**
     * The fully qualified Java class name of the exception that is to be
     * handled by this handler.
     */
    protected $type = null;

    public function getType() {
        return ($this->type);
    }

    public function setType($type) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->type = $type;
    }


    // --------------------------------------------------------- Public Methods


    /**
     * Freeze the configuration of this component.
     */
    public function freeze() {
        $this->configured = true;
    }


    /**
     * Return a String representation of this object.
     */
    public function __toString() {
        $sb = "Aloi_Phruts_Config_ExceptionConfig[";
        $sb .= "type=";
        $sb .= $this->type;
        if ($this->bundle != null) {
            $sb .= ",bundle=";
            $sb .= $this->bundle;
        }
        $sb .= ",key=";
        $sb .= $this->key;
        $sb .= ",path=";
        $sb .= $this->path;
        $sb .= ",scope=";
        $sb .= $this->scope;
        $sb .= "]";
        return $sb;
    }
}
?>