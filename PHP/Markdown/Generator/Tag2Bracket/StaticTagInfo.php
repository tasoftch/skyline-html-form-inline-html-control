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


class StaticTagInfo implements TagInfoInterface
{
	protected $openTags = [
	];

	protected $closeTags = [
	];

	/**
	 * StaticTagInfo constructor.
	 * @param array $openTags
	 * @param array|null $closeTags
	 */
	public function __construct(array $openTags = NULL, array $closeTags = NULL)
	{
		if(NULL !== $openTags) {
			$this->openTags = $openTags;
			$this->closeTags = $closeTags === NULL ? array_map(function($t) {
				$rp = function($t) { return preg_replace("%<([a-z0-9_\-]+)>%", "</$1>", $t); };
				if(is_string($t))
					return $rp($t);
				elseif(is_array($t)) {
					foreach($t as &$v)
						$v = $rp($v);
				}
				return $t;
			}, $openTags) : $closeTags;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function hasTagName(string $tagName, int $options): bool {
		if($options & static::IS_OPEN_TAG_OPTION)
			return isset($this->openTags[$tagName]);
		if($options & static::IS_CLOSE_TAG_OPTION)
			return isset($this->closeTags[$tagName]);
		return false;
	}

	/**
	 * @param array|string[] $openTags
	 * @return static
	 */
	public function setOpenTags($openTags)
	{
		$this->openTags = $openTags;
		return $this;
	}

	/**
	 * @param array|string[] $closeTags
	 * @return static
	 */
	public function setCloseTags($closeTags)
	{
		$this->closeTags = $closeTags;
		return $this;
	}

	/**
	 * Adds an open tag info to the description
	 *
	 * @param string $name
	 * @param string|array|callable $info
	 * @return static
	 */
	public function addOpenTag(string $name, $info) {
		$this->openTags[$name] = $info;
		return $this;
	}

	/**
	 * Adds a close tag info to the description
	 *
	 * @param string $name
	 * @param string|array|callable $info
	 * @return static
	 */
	public function addCloseTag(string $name, $info) {
		$this->closeTags[$name] = $info;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getTagInfo(string $tag, int $options): ?string
	{
		$value = NULL;
		if($options & static::IS_OPEN_TAG_OPTION)
			$value = $this->openTags[ $tag ] ?? NULL;
		elseif($options & static::IS_CLOSE_TAG_OPTION)
			$value = $this->closeTags[ $tag ] ?? NULL;

		repeat:
		if(is_array($value)) {
			$value = $value[($options & static::IS_EDITOR_OPTION | $options & static::IS_PARSER_OPTION) ? 1 : 0] ?? NULL;
			goto repeat;
		} elseif(is_callable($value)) {
			$value = call_user_func($value, $options, $tag);
			goto repeat;
		}

		if($value !== NULL && $options & self::IS_PARSER_OPTION)
			return preg_replace(["/</", "/>/"], ["[", "]"], $value);
		return $value;
	}
}