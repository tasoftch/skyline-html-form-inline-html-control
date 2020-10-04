<?php
/*
 * BSD 3-Clause License
 *
 * Copyright (c) 2019, TASoft Applications
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 *  Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 *  Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace Skyline\HTML\Form\Markdown\Generator\Tag2Bracket;


use Skyline\HTML\Form\Exception\MarkdownTagException;

/**
 * Class AttributedTagInfo reads and parses attributes from bracket markers:
 *
 * [tag:attr1=my-value-1&attr2=my-value-2]
 * [tag] => ok
 * [tag:] => invalid
 * [tag:attr] => valid, declared but not defined attr
 * [tag:attr=3] => valid, declared and defined attr => attr: 3
 * Declared attributes inherit the default value if available in the attribute description
 *
 * @package Skyline\HTML\Form\Markdown\Generator\Tag2Bracket
 */
abstract class AbstractAttributedTagInfo implements TagInfoInterface
{
	/** @var int Attribute is required */
	const ATTR_REQUIRED = 1<<0;

	/** @var int Attribute has default value (array entry) */
	const ATTR_DEFAULT = 1<<1;

	/** @var int Default value is equal to attribute name */
	const ATTR_DEFAULT_ATTR = 1<<2;

	/** @var int Attribute value must be part of a list */
	const ATTR_IN_LIST = 1<<3;

	const ATTR_QUOTES = '"';

	protected $attributeDescriptions = [
//		'attr' => self::ATTR_REQUIRED,
//		'optional' => [
//			self::ATTR_DEFAULT,
//			'default-value'
//		],
//		'required-default' => [
//			self::ATTR_DEFAULT|self::ATTR_REQUIRED,
//			'default'
//		]
	];

	public function hasTagName(string $tagName, int $options): bool
	{
		return strcasecmp(trim($tagName), $this->getTagName()) === 0;
	}


	/**
	 * @return mixed
	 */
	abstract public function getTagName(): string;

	/**
	 * @param $name
	 * @param $parsedValue
	 * @param int $options
	 * @return string|null
	 */
	protected function getAttributeValue($name, $parsedValue, int $options): ?string {
		if(!is_null($v = $this->attributeDescriptions[ $name ] ?? NULL)) {
			if($parsedValue === NULL) {
				$parsedValue = $this->makeDefaultAttributeValue($name, $options);
			}

			if(!$this->isValidAttributeValue($name, $parsedValue, $options))
				throw (new MarkdownTagException("Invalid attribute value for $name", 66))->setTagInfo($this);

			return $parsedValue;
		}
		return NULL;
	}

	/**
	 * Makes a default value, if the attribute was not defined
	 *
	 * @param $name
	 * @param int $options
	 * @return string|null
	 */
	protected function makeDefaultAttributeValue($name, int $options): ?string {
		if(is_array($v = $this->attributeDescriptions[ $name ])) {
			list($o, $d) = $v;
			if($o & self::ATTR_DEFAULT)
				return is_callable($d) ? call_user_func($d, $name, $options) : $d;
		}
		return NULL;
	}

	/**
	 * @param $name
	 * @param $value
	 * @param int $options
	 * @return bool
	 */
	protected function isValidAttributeValue($name, &$value, int $options): bool {
		if(is_array($v = $this->attributeDescriptions[ $name ])) {
			@ list($o, $d, $d1) = $v;
			if($o & self::ATTR_IN_LIST) {
				$d = is_array($d) || is_callable($d) ? $d : $d1;
				if(is_array($d)) {
					$value = $d[$value] ?? $value;
					return in_array($value, $d);
				}
				if(is_callable($d) && !call_user_func($d, $name, $value, $options))
					return false;
			}
		}
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getTagInfo(string $tag, int $options): ?string
	{
		if(strpos($tag, ':') !== false) {
			list($tag, $args) = explode(":", $tag, 2);
			if(strcasecmp(trim($tag), $this->getTagName()) == 0) {
				$attributes = [];

				foreach(explode("&", $args) as $arg) {
					if(strpos($arg, "="))
						list($key, $value) = explode("=", trim($arg), 2);
					else {
						$value = NULL;
						$key = trim($arg);
					}

					$value = $this->getAttributeValue($key, $value, $options);
					if(NULL !== $value)
						$attributes[$key] = $value;
				}

				array_walk($this->attributeDescriptions, function($v, $k) use ($attributes) {
					$o = is_array($v) ? $v[0] : $v;
					if($o & self::ATTR_REQUIRED && !isset($attributes[$k])) {
						throw (new MarkdownTagException("Attribute $k is required for tag ".$this->getTagName(), 99))->setTagInfo($this);
					}
				});

				array_walk($attributes, function(&$v, $k) {
					$q = static::ATTR_QUOTES;
					$v = sprintf("%s=$q%s$q", urlencode($k), urlencode($v));
				});
				return $attributes ? sprintf("<%s %s>", $this->getTagName(), implode(" ", $attributes)) : sprintf("<%s>", $this->getTagName());
			}
		} elseif (strcasecmp(trim($tag), $this->getTagName()) == 0) {
			return $options & self::IS_CLOSE_TAG_OPTION ? sprintf("</%s>", $this->getTagName()) : sprintf("<%s>", $this->getTagName());
		}
		return NULL;
	}
}