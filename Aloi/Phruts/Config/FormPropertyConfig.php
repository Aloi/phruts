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
 * <p>A PHPBean representing the configuration information of a <code>&lt;form-
 * property&gt;</code> element in a Phruts configuration file.<p>
 * @author Cameron MANDERSON <cameronmanderson@gmail.com> (Aloi contributor)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @since Struts 1.1
 * @version $Id$
 */

class Aloi_Phruts_Config_FormPropertyConfig {

	const TYPE_BOOLEAN = 'FormPropertyConfigBoolean';
	const TYPE_STRING = 'FormPropertyConfigString';
	const TYPE_FLOAT = 'FormPropertyConfigFloat';
	const TYPE_INTEGER = 'FormPropertyConfigInteger';
	const TYPE_ARRAY = 'FormPropertyConfigArray';
	
	protected $index = false;
	
	
	
    // ----------------------------------------------------------- Constructors


    /**
     * Constructor that preconfigures the relevant properties.
     *
     * @param string name Name of this property
     * @param string type Fully qualified class name of this property
     * @param string initial Initial value of this property (if any)
     * @param size Size of the array to be created if this property is an  array
     * with no defined initial value
     */
    public function __construct() {
//        $this->setName($name);
//        $this->setType($type);
//        $this->setInitial($initial);
//        $this->setSize($size);
    }




    // ----------------------------------------------------- Instance Variables


    /**
     * Has this component been completely configured?
     */
    protected $configured = false;


    // ------------------------------------------------------------- Properties


    /**
     * String representation of the initial value for this property.
     */
    protected $initial = null;

    public function getInitial() {
        return ($this->initial);
    }

    public function setInitial($initial) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->initial = $initial;
    }


    /**
     * The JavaBean property name of the property described by this element.
     */
    protected $name = null;

    public function getName() {
        return ($this->name);
    }

    public function setName($name) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        $this->name = $name;
    }


    /**
     * <p>The size of the array to be created if this property is an array
     * type and there is no specified <code>initial</code> value.  This
     * value must be non-negative.</p>
     *
     * @since Struts 1.1
     */
    protected $size = 0;
    public function getSize() {
        return ($this->size);
    }
    public function setSize($size) {
        if ($this->configured) {
            throw new Aloi_Serphlet_Exception_IllegalState("Configuration is frozen");
        }
        if ($this->size < 0) {
            throw new Aloi_Serphlet_Exception_IllegalArgument("size < 0");
        }
        $this->size = $size;
    }
        


    /**
     * The fully qualified Java class name of the implementation class
     * of this bean property, optionally followed by <code>[]</code> to
     * indicate that the property is indexed.
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


    /**
     * Return a Class corresponds to the value specified for the
     * <code>type</code> property.
     */
    public function getTypeClass() {

        // Identify the base class (in case an array was specified)
 		$baseType = $this->getType();
        $indexed = false;
//        if (baseType.endsWith("[]")) {
//            baseType = baseType.substring(0, baseType.length() - 2);
//            indexed = true;
//        }

        // Construct an appropriate Class instance for the base class
        $baseClass = null;
        if ($baseType == "boolean") {
            $baseClass = Aloi_Phruts_Config_FormPropertyConfig::TYPE_BOOLEAN;
        } else if ($baseType == "float") {
            $baseClass = Aloi_Phruts_Config_FormPropertyConfig::TYPE_FLOAT;
        } else if ($baseType == "int") {
            $baseClass = Aloi_Phruts_Config_FormPropertyConfig::TYPE_INTEGER;
        } else if ($baseType == "string" || !trim($baseType)) {
            $baseClass = Aloi_Phruts_Config_FormPropertyConfig::TYPE_STRING;
        } else if ($baseType == "array") {
            $baseClass = Aloi_Phruts_Config_FormPropertyConfig::TYPE_ARRAY;
        } else {
            try {
	            if (substr($baseType, strlen($baseType) - 2) == '[]') {
		        	$baseType = substr($baseType, 0, strlen($baseType) -2);
		        	$this->indexed = true;	
		        }
	            Aloi_Serphlet_ClassLoader::loadClass($baseType);
            } catch (Exception $e) {
                $baseClass = null;
            }
        }

        // Return the base class or an array appropriately
        return ($baseClass);
    }



    // --------------------------------------------------------- Public Methods


    /**
     * <p>Return an object representing the initial value of this property.
     * This is calculated according to the following algorithm:</p>
     * <ul>
     * <li>If the value you have specified for the <code>type</code>
     *     property represents an array (i.e. it ends with "[]"):
     *     <ul>
     *     <li>If you have specified a value for the <code>initial</code>
     *         property, <code>ConvertUtils.convert()</code> will be
     *         called to convert it into an instance of the specified
     *         array type.</li>
     *     <li>If you have not specified a value for the <code>initial</code>
     *         property, an array of the length specified by the
     *         <code>size</code> property will be created.  Each element
     *         of the array will be instantiated via the zero-args constructor
     *         on the specified class (if any).  Otherwise, <code>null</code>
     *         will be returned.</li>
     *     </ul></li>
     * <li>If the value you have specified for the <code>type</code>
     *     property does not represent an array:
     *     <ul>
     *     <li>If you have specified a value for the <code>initial</code>
     *         property, <code>ConvertUtils.convert()</code>
     *         will be called to convert it into an object instance.</li>
     *     <li>If you have not specified a value for the <code>initial</code>
     *         attribute, Struts will instantiate an instance via the
     *         zero-args constructor on the specified class (if any).
     *         Otherwise, <code>null</code> will be returned.</li>
     *     </ul></li>
     * </ul>
     */
    public function initial() {
        $initialValue = null;
        try {
            $className = $this->getTypeClass();
            switch($className) {
            	case Aloi_Phruts_Config_FormPropertyConfig::TYPE_ARRAY:
            		if ($this->initial != null) {
	                    $initialValue = explode(',', $this->initial);
	                } else {
	                    $initialValue = array();
	                }
            		break;
            	case Aloi_Phruts_Config_FormPropertyConfig::TYPE_BOOLEAN:
            		$value = $this->initial;
			        if ($value == null)
			            $initialValue = true;
			        else if (strtolower($value) == "true")
			            $initialValue = true;
			        else if (strtolower($value) == "yes")
			            $initialValue = true;
			        else
			            $initialValue = false;
            		break;
            	case Aloi_Phruts_Config_FormPropertyConfig::TYPE_STRING:
            		$initialValue = $this->initial;
            		break;
            	case Aloi_Phruts_Config_FormPropertyConfig::TYPE_FLOAT:
            		if($this->initial != null) {
            			$initialValue = floatval($this->initial);
            		} else $initialValue = floatval(0);
            		break;
        		case Aloi_Phruts_Config_FormPropertyConfig::TYPE_INTEGER:
        			if($this->initial != null) {
        				$initialValue = intval($this->initial);
        			} else $initialValue = intval(0);
            		break;
            	default:
            		// Create the class
            		if($this->indexed) {
            			// Place a indexed set of objects into the form
            			$initialValue = array();
            			$size = intval($this->initial);
            			if($size > 0) {
            				for($x = 0; $x < $size; $x++) {
            					$intialValue[] = Aloi_Serphlet_ClassLoader::newInstance($className);
            				}
            			}
            		} else {
            			$initialValue = Aloi_Serphlet_ClassLoader::newInstance($className);
            		}
            		break;
            }
        } catch (Exception $e) {
            $initialValue = null;
        }
        return ($initialValue);
    }
	
	
    /**
     * Freeze the configuration of this component.
     */
    public function freeze() {
        $this->configured = true;
    }

    /**
     * Return a String representation of this object.
     */
    public function toString() {
        $sb = "Aloi_Phruts_Config_FormPropertyConfig[";
        $sb .= "name=";
        $sb .= $this->name;
        $sb .= ",type=";
        $sb .= $this->type;
        $sb .= ",initial=";
        $sb .= $this->initial;
        $sb .= "]";
        return $sb;
    }
}
?>