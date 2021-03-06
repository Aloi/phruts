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
 * General purpose abstract class that describes an API for retrieving
 * Locale-sensitive messages from underlying resource locations of an
 * unspecified design.
 * 
 * <p>Calls to <samp>getMessage</samp> with a null Locale argument are
 * presumed to be requesting a message string in the message resources default
 * behaviour(implemented by the concrete subclass).</p>
 * <p>Calls to <samp>getMessage</samp> with an unknown key, or an unknown
 * Locale will return null if the <samp>returnNull</samp> property is
 * set to true. Otherwise, a suitable error message will be returned
 * instead.</p>
 *
 * @author Olivier HENRY <oliv.henry@gmail.com> (PHP5 port of Struts)
 * @author John WILDENAUER <jwilde@users.sourceforge.net> (PHP4 port of Struts)
 * @version $Id$
 */
abstract class Aloi_Phruts_Util_MessageResources {
	/**
	 * Logging instance.
	 * 
	 * @var Logger
	 */
	protected static $log = null;

	/**
	 * The configuration parameter used to initialize this
	 * MessageResources.
	 *
	 * @var string
	 */
	protected $config = null;

	/**
	 * The default Locale for our environment.
	 *
	 * @var Locale
	 */
	protected $defaultLocale = null;

	/**
	 * The set of previously created formated messages, keyed by the key computed
	 * in messageKey method.
	 * 
	 * @var array
	 */
	protected $formats = array ();

	/**
	 * Indicate if a null is returned instead of an error message string when
	 * an unknown Locale or key is requested.
	 * 
	 * @var boolean
	 */
	protected $returnNull = false;

	public function __wakeup() {
		if (is_null(self :: $log)) {
			self :: $log = Aloi_Util_Logger_Manager :: getLogger(__CLASS__);
		}
	}

	/**
	 * Construct a new MessageResources according to the specified
	 * parameters.
	 *
	 * @param string $config The configuration parameter for this
	 * MessageResources
	 * @param boolean $returnNull The returnNull property we should initialize
	 * with
	 */
	public function __construct($config, $returnNull = false) {
		if (is_null(self :: $log)) {
			self :: $log = Aloi_Util_Logger_Manager :: getLogger(__CLASS__);
		}

		$this->defaultLocale = Aloi_Util_Locale :: getDefault();

		$this->config = (string) $config;
		$this->returnNull = (boolean) $returnNull;
	}

	/**
	 * The configuration parameter used to initialize this
	 * MessageResources.
	 * 
	 * @return string Parameter used to initialize this MessageResources
	 */
	public function getConfig() {
		return $this->config;
	}

	/** 
	 * Indicates that a null is returned instead of an error message string
	 * if an unknown Locale or key is requested.
	 * 
	 * @return boolean True if null is returned if unknown key or locale
	 * is requested
	 */
	public function getReturnNull() {
		return $this->returnNull;
	}

	/**
	 * Indicates that a null is returned instead of an error message string
	 * if an unknown locale or key is requested.
	 *
	 * @param boolean $returnNull True indicates that a null is returned
	 * if an unknown locale or key is requested.
	 */
	public function setReturnNull($returnNull) {
		$this->returnNull = (boolean) $returnNull;
	}

	/**
	 * Returns a text message after parametric replacement of the specified
	 * parameter placeholders.
	 * 
	 * @param Locale $locale The requested message Locale, or null
	 * for the system default Locale
	 * @param string $key The message key to look up
	 * @param string $arg0 The replacement for placeholder {0} in the message
	 * @param string $arg1 The replacement for placeholder {1} in the message
	 * @param string $arg2 The replacement for placeholder {2} in the message
	 * @param string $arg3 The replacement for placeholder {3} in the message
	 * @return string
	 */
	public function getMessage($locale, $key, $arg0 = null, $arg1 = null, $arg2 = null, $arg3 = null) {
		$formatKey = $this->messageKey($locale, $key);

		if (array_key_exists($formatKey, $this->formats)) {
			$formatString = $this->formats[$formatKey];
		} else {
			$formatString = $this->getBaseMessage($locale, $key);

			if (is_null($formatString)) {
				if ($this->returnNull) {
					return null;
				} else {
					return '???' . $formatKey . '???';
				}
			}

			$this->formats[$formatKey] = $formatString;
		}

		return $this->formatMessage($formatString, $arg0, $arg1, $arg2, $arg3);
	}

	/**
	 * Returns a text message for the specified key and the specified
	 * Locale.
	 * 
	 * <p>A null string result will be returned by this method if no relevant
	 * message resource is found for this key or Locale, if the returnNull
	 * property is set. Otherwise, an appropriate error message will be
	 * returned.</p>
	 * <p>This method must be implemented by a concrete subclass.</p>
	 * @param locale $locale The requested message Locale, or null
	 * for the system default Locale
	 * @param string $key The message key to look up
	 */
	protected abstract function getBaseMessage($locale, $key);

	/**
	 * Return true if there is a defined message for the specified key
	 * in the specified Locale.
	 *
	 * @param Locale $locale The requested message Locale, or null
	 * for the system default Locale
	 * @param string $key The message key to look up
	 * @return boolean
	 * @todo Check if the parameter is a Locale object.
	 */
	public function isPresent($locale, $key) {
		$message = $this->getBaseMessage($locale, $key);
		if (is_null($message)) {
			return false;
		}
		elseif (preg_match('`^\?{3}.*\?{3}$`', $message)) {
			return false; // FIXME - Only valid for default implementation
		} else {
			return true;
		}
	}

	/**
	 * Compute and return a key to be used in caching information by
	 * a Locale.
	 * 
	 * <b>NOTE:</b> The locale key for the default Locale in our
	 * environment is a zero length String.
	 * @param Locale $locale The locale for which a key is desired
	 * @return string
	 * @todo Check if the parameter is a Locale object.
	 */
	protected function localeKey($locale) {
		if (is_null($locale))
			return '';
		else if($locale instanceof Locale)
			return $locale->__toString();
		else 
			return '';
	}

	/**
	 * Compute and return a key to be used in caching information by
	 * Locale and message key.
	 *
	 * @param Locale $locale The locale key for which this cache key
	 * is calculated
	 * @param string $key The message key for which this cache key is calculated
	 * @return string
	 */
	protected function messageKey($locale, $key) {
		return $this->localeKey($locale) . '.' . $key;
	}

	/**
	 * Compute and return a key to be used in caching information by locale
	 * key and message key.
	 *
	 * @param string $localeKey The locale key for which this cache key is
	 * calculated
	 * @param string $key The message key for which this cache key is calculated
	 * @return string
	 */
	protected function messageKeyByLocaleKey($localeKey, $key) {
		return $localeKey . '.' . $key;
	}

	/**
	 * Format the message pattern by replacing the {0}-{3} parameters
	 * with the corresponding args array parameters.
	 * 
	 * @param string $message The message pattern to format
	 * @param string $arg0 The replacement for placeholder {0} in the message
	 * @param string $arg1 The replacement for placeholder {1} in the message
	 * @param string $arg2 The replacement for placeholder {2} in the message
	 * @param string $arg3 The replacement for placeholder {3} in the message
	 * @return string Returns the formatted message string.
	 */
	private function formatMessage($message, $arg0 = null, $arg1 = null, $arg2 = null, $arg3 = null) {
		if(!is_array($arg0)) {
			$args = array ();
			if (!is_null($arg0))
				$args[0] = (string) $arg0;
			if (!is_null($arg1))
				$args[1] = (string) $arg1;
			if (!is_null($arg2))
				$args[2] = (string) $arg2;
			if (!is_null($arg3))
				$args[3] = (string) $arg3;
		} else {
			$args = array_slice($args, 0, 4);
		}
		$params = array (
			'{0}',
			'{1}',
			'{2}',
			'{3}'
		);

		foreach ($args as $key => $value) {
			$message = str_replace($params[$key], $value, $message);
		}

		return $message;
	}
}
?>
