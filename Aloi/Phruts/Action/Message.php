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
 * <p>An encapsulation of an individual message returned by the
 * <code>validate()</code> method of an <code>Aloi_Phruts_Action_Form</code>, consisting
 * of a message key (to be used to look up message text in an appropriate
 * message resources database) plus up to four placeholder objects that can
 * be used for parametric replacement in the message text.</p>
 *
 * @author Cameron MANDERSON <cameronmanderson@gmail.com>
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 * @since Struts 1.1
 */
class Aloi_Phruts_Action_Message {
	/**
	 * The message key for this message.
	 * 
	 * @var string
	 */
	protected $key = null;

	/**
	 * The replacement values for this message.
	 * 
	 * @var array
	 */
	protected $values = null;

	/**
	 * Construct an action message with the specified replacement values.
	 *
	 * @param string $key The Message key for this message
	 * @param string $value0 First replacement value
	 * @param string $value1 Second replacement value
	 * @param string $value2 Third replacement value
	 * @param string $value3 Fourth replacement value
	 */
	public function __construct($key, $value0 = '', $value1 = '', $value2 = '', $value3 = '') {
		$this->key = (string) $key;

		$this->values = array (
			(string) $value0,
			(string) $value1,
			(string) $value2,
			(string) $value3
		);
	}

	/**
	 * Get the message key for this message.
	 * 
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Get the replacement values for this message.
	 * 
	 * @return array
	 */
	public function getValues() {
		return $this->values;
	}
}
?>