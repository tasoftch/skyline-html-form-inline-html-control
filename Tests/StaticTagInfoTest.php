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

use PHPUnit\Framework\TestCase;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\StaticTagInfo;

class StaticTagInfoTest extends TestCase
{
	public function testStaticTagsConstructor() {
		$ti = new StaticTagInfo(['a' => '<a>', 'b' => '<b>'], []);

		$this->assertEquals("<a>", $ti->getTagInfo('a', $ti::IS_OPEN_TAG_OPTION));
		$this->assertEquals("<b>", $ti->getTagInfo('b', $ti::IS_OPEN_TAG_OPTION|$ti::IS_EDITOR_OPTION));

		$this->assertNull($ti->getTagInfo('c', $ti::IS_OPEN_TAG_OPTION));
		$this->assertNull($ti->getTagInfo('a', $ti::IS_CLOSE_TAG_OPTION));
		$this->assertNull($ti->getTagInfo('a', $ti::IS_EDITOR_OPTION));

		// Automatically creates close tags if no argument is passed.
		$ti = new StaticTagInfo(['a' => '<a>']);

		$this->assertEquals("<a>", $ti->getTagInfo('a', $ti::IS_OPEN_TAG_OPTION));
		$this->assertEquals("</a>", $ti->getTagInfo('a', $ti::IS_CLOSE_TAG_OPTION));

		$ti = new StaticTagInfo(['a' => '<a>'], ['a' => '</c>']);
		$this->assertEquals("<a>", $ti->getTagInfo('a', $ti::IS_OPEN_TAG_OPTION));
		$this->assertEquals("</c>", $ti->getTagInfo('a', $ti::IS_CLOSE_TAG_OPTION));
	}

	public function testArrayTagInfo() {
		$ti = new StaticTagInfo(['a' => [
			StaticTagInfo::IS_OPEN_TAG_OPTION => '<a>',
			StaticTagInfo::IS_CLOSE_TAG_OPTION => '</a>',
			StaticTagInfo::IS_OPEN_TAG_OPTION | StaticTagInfo::IS_EDITOR_OPTION => '<b>',
			StaticTagInfo::IS_CLOSE_TAG_OPTION | StaticTagInfo::IS_EDITOR_OPTION => '</b>'
		]]);

		$this->assertEquals("<a>", $ti->getTagInfo('a', $ti::IS_OPEN_TAG_OPTION));
		$this->assertEquals("</a>", $ti->getTagInfo('a', $ti::IS_CLOSE_TAG_OPTION));

		$this->assertEquals("<b>", $ti->getTagInfo('a', $ti::IS_OPEN_TAG_OPTION | $ti::IS_EDITOR_OPTION));
		$this->assertEquals("</b>", $ti->getTagInfo('a', $ti::IS_CLOSE_TAG_OPTION | $ti::IS_EDITOR_OPTION));
	}

	public function testCallbackTagInfo() {
		$ti = new StaticTagInfo(['a' => function($options, $tag) use (&$myopts, &$myTag) {
			$myopts = $options;
			$myTag = $tag;
			return "<s>";
		}]);

		$this->assertNull($ti->getTagInfo('b', $ti::IS_OPEN_TAG_OPTION));
		$this->assertEquals("<s>", $ti->getTagInfo("a", $ti::IS_OPEN_TAG_OPTION | $ti::IS_EDITOR_OPTION));

		$this->assertSame($ti::IS_OPEN_TAG_OPTION | $ti::IS_EDITOR_OPTION, $myopts);
		$this->assertSame('a', $myTag);
	}
}
