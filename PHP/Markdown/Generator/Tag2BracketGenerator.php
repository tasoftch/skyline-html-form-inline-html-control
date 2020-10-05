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

namespace Skyline\HTML\Form\Markdown\Generator;


use Skyline\HTML\Form\Exception\MarkdownException;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\TagInfoInterface;

class Tag2BracketGenerator implements MarkdownGeneratorValidatorInterface
{
	/** @var TagInfoInterface */
	private $tagInfo;

	/**
	 * Tag2BracketGenerator constructor.
	 * @param TagInfoInterface $tagInfo
	 */
	public function __construct(TagInfoInterface $tagInfo)
	{
		$this->tagInfo = $tagInfo;
	}


	public function canGenerateFromInput(string $plainEditorInput): bool
	{
		if(preg_match_all("%<(/?)([^>]*)>%i", $plainEditorInput, $ms)) {
			for($e=0;$e<count($ms[0]);$e++) {
				$isClose = $ms[1][$e] == '/';
				$tag = $ms[2][$e];
				if(!$this->getTagInfo()->hasTagName($tag, $isClose == '/' ? $this->getTagInfo()::IS_CLOSE_TAG_OPTION : $this->getTagInfo()::IS_OPEN_TAG_OPTION))
					return false;
			}
		}
		return true;
	}


	/**
	 * @inheritDoc
	 */
	public function generateFromInput(string $plainEditorInput)
	{
		try {
			// Strip empty and nonsense tags
			$plainEditorInput = preg_replace_callback("/<([^>]+)>\s*<\/([^>]+)>/", function($ms) {
				list(,$o, $c) = $ms;
				if(strcasecmp($o, $c) == 0)
					return "";
				return $ms[0];
			}, $plainEditorInput);

			$plainEditorInput = preg_replace([
				"/(<br>)+/",
				"/&nbsp;/i"
			], [
				"<br>",
				" "
			], $plainEditorInput);

			$ti = $this->getTagInfo();
			// Try to parse tag names into tag markers objects.
			return preg_replace_callback("%<(/?)([^>]*)>%i", function($ms) use ($ti) {
				list(,$isClose, $name) = $ms;

				if(@$doc = simplexml_load_string("<$name/>")) {
					$attrs = [];
					foreach($doc->attributes() as $n => $v) {
						$attrs[] = sprintf("%s=%s", urlencode( $n ), urlencode( $v ) );
					}
					$name = sprintf("%s:%s", $doc->getName(), implode("&", $attrs));
				}

				if(NULL !== ($n = $ti->getTagInfo($name, $ti::IS_PARSER_OPTION | ($isClose == '/' ? $ti::IS_CLOSE_TAG_OPTION : $ti::IS_OPEN_TAG_OPTION)))) {
					return $n;
				}
				throw new MarkdownException("No tag info found for $name", 77);
			}, $plainEditorInput);
		} catch (MarkdownException $e) {
			throw $e->setGenerator($this);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function generateHTML($markdown): ?string
	{
		$ti = $this->getTagInfo();
		$html = preg_replace_callback("%\[(/?)([^]]*)]%i", function($ms) use ($ti) {
			list(,$isClose, $name) = $ms;
			return $ti->getTagInfo($name, $isClose == '/' ? $ti::IS_CLOSE_TAG_OPTION : $ti::IS_OPEN_TAG_OPTION);
		}, $markdown);
		return preg_replace([
			"/[\n\r]/",
			"/\s/"
		], [
			"<br>",
			"&nbsp;"
		], $html);
	}

	/**
	 * @inheritDoc
	 */
	public function generateInput($markdown): ?string
	{
		$ti = $this->getTagInfo();
		$html = preg_replace_callback("%\[(/?)([^]]*)]%i", function($ms) use ($ti) {
			list(,$isClose, $name) = $ms;
			$tag = $ti->getTagInfo($name, $ti::IS_EDITOR_OPTION | ($isClose == '/' ? $ti::IS_CLOSE_TAG_OPTION : $ti::IS_OPEN_TAG_OPTION));
			return preg_replace("/\s/", "°°°°", $tag);
		}, $markdown);
		return preg_replace([
			"/[\n\r]/",
			"/\s/",
			"/°°°°/"
		], [
			"<br>",
			"&nbsp;",
			" "
		], $html);
	}

	/**
	 * @return TagInfoInterface
	 */
	public function getTagInfo(): TagInfoInterface
	{
		return $this->tagInfo;
	}
}