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


class TextTagInfo extends StaticTagInfo
{
	protected $openTags = [
		"br" => "\n",
		"p" => '<p>',
		"b" => ["<strong>", "<b>"],
		"strong" => ["<strong>", "<b>"],
		"i" => ['<em>', '<i>'],
		"em" => ['<em>', '<i>'],
		"u" => '<u>',
		"strike" => '<s>',
		"s" => '<s>',
		"h1" => '<h1>',
		"h2" => '<h2>',
		"h3" => '<h3>',
		"h4" => '<h4>',
		"h5" => '<h5>',
		"h6" => '<h6>',
		"div" => '<div>',
		"span" => ''
	];

	protected $closeTags = [
		"p" => '</p>',
		"b" => ["</strong>", "</b>"],
		"strong" => ["</strong>", "</b>"],
		"i" => ['</em>', '</i>'],
		"em" => ['</em>', '</i>'],
		"u" => '</u>',
		"strike" => '</s>',
		"s" => '</s>',
		"h1" => '</h1>',
		"h2" => '</h2>',
		"h3" => '</h3>',
		"h4" => '</h4>',
		"h5" => '</h5>',
		"h6" => '</h6>',
		"div" => '</div>',
		"span" => ''
	];

	public function getTagInfo(string $tag, int $options): ?string
	{
		if(stripos($tag, 'span') === 0)
			$tag = 'span';
		return parent::getTagInfo($tag, $options);
	}
}