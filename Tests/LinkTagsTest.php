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

use PHPUnit\Framework\TestCase;
use Skyline\HTML\Form\Exception\MarkdownTagException;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\LinkTagInfo;

class LinkTagsTest extends TestCase
{
	public function testHasTag() {
		$link = new LinkTagInfo();
		$this->assertTrue($link->hasTagName('a', $link::IS_OPEN_TAG_OPTION));
		$this->assertTrue($link->hasTagName('A', $link::IS_CLOSE_TAG_OPTION));

		$this->assertFalse($link->hasTagName('b', $link::IS_OPEN_TAG_OPTION));
		$this->assertFalse($link->hasTagName('U', $link::IS_OPEN_TAG_OPTION|$link::IS_EDITOR_OPTION));
	}

	public function testCloseTag() {
		$link = new LinkTagInfo();

		$this->assertEquals("</A>", $link->getTagInfo("a", $link::IS_CLOSE_TAG_OPTION));
		$this->assertEquals("</A>", $link->getTagInfo("a", $link::IS_CLOSE_TAG_OPTION|$link::IS_EDITOR_OPTION));

		$this->assertNull($link->getTagInfo("b", $link::IS_CLOSE_TAG_OPTION));
	}

	public function testOpenTag() {
		$link = new LinkTagInfo();

		$this->assertEquals("<A>", $link->getTagInfo("a", $link::IS_OPEN_TAG_OPTION));
		$this->assertEquals("<A>", $link->getTagInfo("a", $link::IS_OPEN_TAG_OPTION|$link::IS_EDITOR_OPTION));
	}

	public function testOpenTagWithAttribute() {
		$link = new LinkTagInfo();

		$this->assertEquals("<A href=\"test\">", $link->getTagInfo("a:href=test", $link::IS_OPEN_TAG_OPTION));
		$this->assertEquals("<A href=\"test\" target=\"_blank\">", $link->getTagInfo("a:href=test&target", $link::IS_OPEN_TAG_OPTION));
		$this->assertEquals("<A href=\"test\" target=\"_parent\">", $link->getTagInfo("a:href=test&target=_parent", $link::IS_OPEN_TAG_OPTION));
		$this->assertEquals("<A href=\"test\" target=\"_blank\">", $link->getTagInfo("a:href=test&target=b", $link::IS_OPEN_TAG_OPTION));

		$this->expectException(MarkdownTagException::class);
		$this->assertEquals("<A href=\"test\" target=\"_he\">", $link->getTagInfo("a:href=test&target=_he", $link::IS_OPEN_TAG_OPTION));
	}
}
