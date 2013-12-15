<?php

/**
 * Security functions.
 */

namespace assegai;

/**
 * This class integrates functions to secure strings and request
 * parameters to compensate for PHP's inability to do this on its
 * own.
 *
 * This file originates from CakePHP under MIT License; many thanks to
 * the CakePHP team for their good work.
 *
 * This file is part of assegai.
 *
 * assegai is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * assegai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with assegai.  If not, see <http://www.gnu.org/licenses/>.
 */
class Security
{
    /**
	 * Removes any non-alphanumeric characters.
	 *
	 * @param string $string String to sanitize
	 * @param array $allowed An array of additional characters that are not to be removed.
	 * @return string Sanitized string
	 */
	function paranoid($string, $allowed = array()) {
		$allow = null;
		if (!empty($allowed)) {
			foreach ($allowed as $value) {
				$allow .= "\\$value";
			}
		}

		if (is_array($string)) {
			$cleaned = array();
			foreach ($string as $key => $clean) {
				$cleaned[$key] = preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $clean);
			}
		} else {
			$cleaned = preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $string);
		}
		return $cleaned;
	}

    /**
	 * Returns given string safe for display as HTML. Renders entities.
	 *
	 * strip_tags() does not validating HTML syntax or structure, so it might strip whole passages
	 * with broken HTML.
	 *
	 * ### Options:
	 *
	 * - remove (boolean) if true strips all HTML tags before encoding
	 * - charset (string) the charset used to encode the string
	 * - quotes (int) see http://php.net/manual/en/function.htmlentities.php
	 * - double (boolean) doube encode html entities
	 *
	 * @param string $string String from where to strip tags
	 * @param array $options Array of options to use.
	 * @return string Sanitized string
	 */
	function html($string, $options = array()) {
		static $defaultCharset = false;
		$defaultCharset = 'UTF-8';
		$default = array(
			'remove' => false,
			'charset' => $defaultCharset,
			'quotes' => ENT_QUOTES,
			'double' => true
			);

		$options = array_merge($default, $options);

		if ($options['remove']) {
			$string = strip_tags($string);
		}

		return htmlentities($string, $options['quotes'], $options['charset'], $options['double']);
	}

    /**
	 * Strips extra whitespace from output
	 *
	 * @param string $str String to sanitize
	 * @return string whitespace sanitized string
	 */
	protected function strip_whitespace($str) {
		$r = preg_replace('/[\n\r\t]+/', '', $str);
		return preg_replace('/\s{2,}/', ' ', $r);
	}

    /**
	 * Strips image tags from output
	 *
	 * @param string $str String to sanitize
	 * @return string Sting with images stripped.
	 */
	protected function strip_images($str) {
		$str = preg_replace('/(<a[^>]*>)(<img[^>]+alt=")([^"]*)("[^>]*>)(<\/a>)/i', '$1$3$5<br />', $str);
		$str = preg_replace('/(<img[^>]+alt=")([^"]*)("[^>]*>)/i', '$2<br />', $str);
		$str = preg_replace('/<img[^>]*>/i', '', $str);
		return $str;
	}

    /**
	 * Strips scripts and stylesheets from output
	 *
	 * @param string $str String to sanitize
	 * @return string String with <script>, <style>, <link> elements removed.
	 */
	protected function strip_scripts($str) {
		return preg_replace('/(<link[^>]+rel="[^"]*stylesheet"[^>]*>|<img[^>]*>|style="[^"]*")|<script[^>]*>.*?<\/script>|<style[^>]*>.*?<\/style>|<!--.*?-->/is', '', $str);
	}

    /**
	 * Strips extra whitespace, images, scripts and stylesheets from output
	 *
	 * @param string $str String to sanitize
	 * @return string sanitized string
	 */
	protected function strip_all($str) {
		$str = $this->strip_whitespace($str);
		$str = $this->strip_images($str);
		$str = $this->strip_scripts($str);
		return $str;
	}

    /**
	 * Strips the specified tags from output. First parameter is string from
	 * where to remove tags. All subsequent parameters are tags.
	 *
	 * Ex.`$clean = Sanitize::stripTags($dirty, 'b', 'p', 'div');`
	 *
	 * Will remove all `<b>`, `<p>`, and `<div>` tags from the $dirty string.
	 *
	 * @param string $str String to sanitize
	 * @param string $tag Tag to remove (add more parameters as needed)
	 * @return string sanitized String
	 */
	protected function strip_tags() {
		$params = func_get_args();
		$str = $params[0];

		for ($i = 1, $count = count($params); $i < $count; $i++) {
			$str = preg_replace('/<' . $params[$i] . '\b[^>]*>/i', '', $str);
			$str = preg_replace('/<\/' . $params[$i] . '[^>]*>/i', '', $str);
		}
		return $str;
	}

    /**
	 * Sanitizes given array or value for safe input. Use the options to specify
	 * the connection to use, and what filters should be applied (with a boolean
	 * value). Valid filters:
	 *
	 * - odd_spaces - removes any non space whitespace characters
	 * - encode - Encode any html entities. Encode must be true for the `remove_html` to work.
	 * - dollar - Escape `$` with `\$`
	 * - carriage - Remove `\r`
	 * - unicode -
	 * - escape - Should the string be SQL escaped.
	 * - backslash -
	 * - remove_html - Strip HTML with strip_tags. `encode` must be true for this option to work.
	 *
	 * @param mixed $data Data to sanitize
	 * @param mixed $options If string, DB connection being used, otherwise set of options
	 * @return mixed Sanitized data
	 */
	function clean($data, $options = array()) {
		if (empty($data)) {
			return $data;
		}

		if (is_string($options)) {
			$options = array('connection' => $options);
		} else if (!is_array($options)) {
			$options = array();
		}

		$options = array_merge(array(
								   'connection' => 'default',
								   'odd_spaces' => true,
								   'remove_html' => false,
								   'encode' => true,
								   'dollar' => true,
								   'carriage' => true,
								   'unicode' => true,
								   'backslash' => true
								   ), $options);

		if (is_array($data)) {
			foreach ($data as $key => $val) {
				$data[$key] = $this->clean($val, $options);
			}
			return $data;
		} else {
			if ($options['odd_spaces']) {
				$data = str_replace(chr(0xCA), '', str_replace(' ', ' ', $data));
			}
			if ($options['encode']) {
				$data = $this->html($data, array('remove' => $options['remove_html']));
			}
			if ($options['dollar']) {
				$data = str_replace("\\\$", "$", $data);
			}
			if ($options['carriage']) {
				$data = str_replace("\r", "", $data);
			}

			$data = str_replace("'", "'", str_replace("!", "!", $data));

			if ($options['unicode']) {
				$data = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $data);
			}
			if ($options['backslash']) {
				$data = preg_replace("/\\\(?!&amp;#|\?#)/", "\\", $data);
			}
			return $data;
		}
	}
}
?>
