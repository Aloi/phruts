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
 * <p>A class that encapsulates messages.  Messages can be either global
 * or they are specific to a particular bean property.</p>
 *
 * <p>Each individual message is described by an <code>Aloi_Phruts_Action_Message</code>
 * object, which contains a message key (to be looked up in an appropriate
 * message resources database), and up to four placeholder arguments used for
 * parametric substitution in the resulting message.</p>
 *
 * <p><strong>IMPLEMENTATION NOTE</strong> - It is assumed that these objects
 * are created and manipulated only within the context of a single thread.
 * Therefore, no synchronization is required for access to internal
 * collections.</p>
 * 
 * @author Cameron Manderson <cameronmanderson@gmail.com> (Aloi Contributor)
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @author David Geary
 * @author Craig R. McClanahan
 * @author David Winterfeldt
 * @author David Graham
 * @version $Id$
 * @since Struts 1.1
 */
class Aloi_Phruts_Action_Messages {
	/**
	 * The "property name" marker to use for global messages, as opposed to those
	 * related to a specific property.
	 */
	const GLOBAL_MESSAGE = 'Aloi_Phruts_Action_GLOBAL_MESSAGE';

	/**
	 * The accumulated set of Aloi_Phruts_Action_Message objects for each property, keyed
	 * by property name.
	 * 
	 * @var array
	 */
	protected $messages = array ();

	/**
	 * The current number of the property/key being added.  This is used
	 * to maintain the order messages are added.
	 * 
	 * @var integer
	 */
	protected $iCount = 0;
	
	protected $accessed = false;

	/**
	 * Add an message message to the set of messages for the specified property.
	 *
	 * An order of the property/key is maintained based on the initial addition
	 * of the property/key.
	 * 
	 * @param string $property Property name (or
	 * Aloi_Phruts_Action_Messages::GLOBAL_MESSAGE)
	 * @param Aloi_Phruts_Action_Message $message The message message to be added
	 */
	public function add($property, Aloi_Phruts_Action_Message $message) {
		$property = (string) $property;

		if (array_key_exists($property, $this->messages)) {
			$item = $this->messages[$property];
			$list = & $item->getList();

			$list[] = $message;
		} else {
			$list = array (
				$message
			);
			$item = new Aloi_Phruts_Action_MessageItem($list, $this->iCount++);

			$this->messages[$property] = $item;
		}
	}
	
	/**
     * <p>Adds the messages from the given <code>Aloi_Phruts_Action_Messages</code> object to
     * this set of messages. The messages are added in the order they are returned from
     * the <code>properties</code> method. If a message's property is already in the current
     * <code>Aloi_Phruts_Action_Messages</code> object, it is added to the end of the list for that
     * property. If a message's property is not in the current list it is added to the end
     * of the properties.</p>
     * 
     * @param Aloi_Phruts_Action_Messages messages The <code>Aloi_Phruts_Action_Messages</code> object to
     * be added.  This parameter can be <code>null</code>.
     * @since Struts 1.1
     */
	public function addMessages(Aloi_Phruts_Action_Messages $messages) {
        if ($messages == null) {
            return;
        }
        
        // loop over properties
        $properties = $messages->properties();
        foreach($properties as $property) {
            // loop over messages for each property
			$msgs = $messages->get($property);
			foreach($msgs as $message) {
				$this->add($property, $message);
			}
        }
	}

	/**
	 * Clear all messages recorded by this object.
	 */
	public function clear() {
		$this->messages = array ();
	}

	/**
	 * Return true if there are no messages recorded in this collection, or false
	 * otherwise.
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return (count($this->messages) == 0);
	}

	/**
	 * Return the set of messages related to a specific property or the set of all
	 * recorded messages (property = "") without distinction by which property the
	 * messages are associated with.
	 * 
	 * If there are no such messages, an empty array is returned.
	 *
	 * @param string $property The property name (or
	 * Aloi_Phruts_Action_Messages::GLOBAL_MESSAGE)
	 * @return array
	 */
	public function get($property = '') {
		$this->accessed = true;
		
		$property = (string) $property;

		if ($property == '') {
			if (count($this->messages) == 0) {
				return array ();
			}

			$messageItems = array ();
			foreach ($this->messages as $messageItem) {
				// Sort Aloi_Phruts_Action_MessageItem based on the initial order the
				// property/key was added to Aloi_Phruts_Action_Messages.
				$messageItems[$messageItem->getOrder()] = $messageItem;
			}

			$results = array ();
			foreach ($messageItems as $messageItem) {
				$items = $messageItem->getList();
				foreach ($items as $item) {
					$results[] = $item;
				}
			}
			return $results;
		} else {
			if (array_key_exists($property, $this->messages)) {
				$item = $this->messages[$property];
				return $item->getList();
			} else {
				return array ();
			}
		}
	}
	
	/**
     * <p>Returns <code>true</code> if the <code>get(String)</code> method
     * has been called.</p>
     *
     * @return <code>true</code> if the messages have been accessed one or more
     * times.
     * @since Struts 1.2
     */
	public function isAccessed() { 
		return $this->accessed;
	}

	/**
	 * Return the set of property names for which at least one message has
	 * been recorded.
	 * 
	 * If there are no messages, an empty array is returned. If you have recorded
	 * global messages, the String value of Aloi_Phruts_Action_Messages::GLOBAL_MESSAGE will
	 * be one of the returned property names.
	 *
	 * @return array
	 */
	public function properties() {
		return array_keys($this->messages);
	}

	/**
	 * Return the number of messages associated with the specified property or
	 * for all properties (including global messages) if property is empty.
	 * 
	 * <b>NOTE</b> - it is more efficient to call <samp>isEmpty</samp> if all
	 * you care about is whether or not there are any messages at all.
	 *
	 * @param string $property The property name (or
	 * Actionmessages::GLOBAL_MESSAGE)
	 * @return integer
	 */
	public function size($property = '') {
		$property = (string) $property;

		if ($property == '') {
			$total = 0;
			foreach ($this->messages as $item) {
				$total += count($item->getList());
			}
			return $total;
		} else {
			if (array_key_exists($property, $this->messages)) {
				$item = $this->messages[$property];
				return count($item->getList());
			} else {
				return 0;
			}
		}
	}
}

/**
 * This class is used to store a set of messages associated with a property/key
 * and the position it was initially added to list.
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
class Aloi_Phruts_Action_MessageItem {
	/**
	 * The list of Aloi_Phruts_Action_Message.
	 * 
	 * @var array
	 */
	protected $list = null;

	/**
	 * The position in the list of messages.
	 * 
	 * @var integer
	 */
	protected $iOrder = 0;

	/**
	 * @param array $list The list of Actionmessages.
	 * @param integer $iOrder The position in the list of messages.
	 */
	public function __construct(array $list, $iOrder) {
		$this->list = $list;
		$this->iOrder = (integer) $iOrder;
	}

	/**
	 * @return array
	 */
	public function & getList() {
		return $this->list;
	}

	/**
	 * @param array $list
	 */
	public function setList(array $list) {
		$this->list = $list;
	}

	/**
	 * @return integer
	 */
	public function getOrder() {
		return $this->iOrder;
	}

	/**
	 * @param integer $iOrder
	 */
	public function setOrder($iOrder) {
		$this->iOrder = (integer) $iOrder;
	}
}
?>