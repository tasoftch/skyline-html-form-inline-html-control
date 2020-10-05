<?php
/*
 * BSD 3-Clause License
 *
 * Copyright (c) 2020, TASoft Applications
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

namespace Skyline\HTML\Form\Control\Text\EditorAction;

/**
 * Class DefaultAction
 *
 * Use instances of this class to setup title and icon of already existing actions.
 *
 * @package Skyline\HTML\Form\Control\Text\EditorAction
 */
class DefaultAction implements EditorActionAwareInterface
{
	const BOLD_ACTION = 'bold';
	const ITALIC_ACTION = 'italic';
	const UNDERLINE_ACTION = 'underline';
	const STRIKE_ACTION = 'strikethrough';
	const HEADING_1_ACTION = "heading1";
	const HEADING_2_ACTION = "heading2";
	const PARAGRAPH_ACTION = "paragraph";
	const BLOCKQUOTE_ACTION = 'quote';
	const ORDERED_LIST_ACTION = 'olist';
	const UNORDERED_LIST_ACTION = 'ulist';
	const CODE_ACTION = 'code';
	const LINE_ACTION = 'line';
	const SIMPLE_LINK_ACTION = 'link';
	const SIMPLE_IMAGE_ACTION = 'image';

	private static $defaultActions;

	/**
	 * Returns a list with the default actions
	 * Please note that those actions are globally available, so if you change one of them
	 *
	 * @return array
	 */
	public static function getDefaultActions(): array {
		if(!self::$defaultActions) {
			self::$defaultActions = [
				static::BOLD_ACTION => new static(static::BOLD_ACTION),
				static::ITALIC_ACTION => new static(static::ITALIC_ACTION),
				static::UNDERLINE_ACTION => new static(static::UNDERLINE_ACTION),
				static::STRIKE_ACTION => new static(static::STRIKE_ACTION),
				static::HEADING_1_ACTION => new static(static::HEADING_1_ACTION),
				static::HEADING_2_ACTION => new static(static::HEADING_2_ACTION),
				static::PARAGRAPH_ACTION => new static(static::PARAGRAPH_ACTION),
				static::BLOCKQUOTE_ACTION => new static(static::BLOCKQUOTE_ACTION),
				static::ORDERED_LIST_ACTION => new static(static::ORDERED_LIST_ACTION),
				static::UNORDERED_LIST_ACTION => new static(static::UNORDERED_LIST_ACTION),
				static::CODE_ACTION => new static(static::CODE_ACTION),
				static::LINE_ACTION => new static(static::LINE_ACTION),
				static::SIMPLE_LINK_ACTION => new static(static::SIMPLE_LINK_ACTION),
				static::SIMPLE_IMAGE_ACTION => new static(static::SIMPLE_IMAGE_ACTION)
			];
		}
		return self::$defaultActions;
	}

	/**
	 * Gets a filtered action list
	 *
	 * Use a callable to decide for each action if it is present in list.
	 * Or specify an array containing the action names.
	 *
	 * Pass exclude to negate the filter.
	 *
	 * @param callable|array $filter
	 * @param bool $exclude
	 * @return array
	 */
	public static function getFilteredDefaultActions($filter, bool $exclude = false): array {
		return array_filter( static::getDefaultActions(), function(EditorActionAwareInterface $action) use ($filter, $exclude): bool {
			$t = $exclude ? false : true;
			$f = $exclude ? true : false;
			if(is_callable($filter))
				return call_user_func($filter, $action) ? $t : $f;
			elseif(is_array($filter))
				return in_array($action->getName(), $filter) ? $t:$f;
			return true;
		} );
	}

	/**
	 * Declare a full editor action as default in the project.
	 *
	 * @param EditorActionInterface $action
	 */
	public static function declareDefault(EditorActionInterface $action) {
		self::getDefaultActions();
		self::$defaultActions[ $action->getName() ] = $action;
	}

	/** @var string */
	private $name;
	/** @var string|null */
	private $title;
	/** @var string|null */
	private $icon;

	/**
	 * DefaultAware constructor.
	 * @param string $name
	 * @param string|null $title
	 * @param string|null $icon
	 */
	public function __construct(string $name, string $title = NULL, string $icon = NULL)
	{
		$this->name = $name;
		$this->title = $title;
		$this->icon = $icon;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * @return string|null
	 */
	public function getIcon(): ?string
	{
		return $this->icon;
	}

	/**
	 * @param string|null $title
	 * @return static
	 */
	public function setTitle(?string $title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @param string|null $icon
	 * @return static
	 */
	public function setIcon(?string $icon)
	{
		$this->icon = $icon;
		return $this;
	}
}