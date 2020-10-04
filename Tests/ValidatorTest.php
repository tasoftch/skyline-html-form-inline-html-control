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
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\TextTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2BracketGenerator;
use Skyline\HTML\Form\Validator\IsHTMLValidator;
use Skyline\HTML\Form\Validator\IsMarkdownValidator;

class ValidatorTest extends TestCase
{
	public function testHTMLValidator() {
		$val = new IsHTMLValidator();

		$this->assertTrue($val->validateValue("<div class='pell'>Contents</div><p><br></p>"));
		$this->assertTrue($val->validateValue("Hello World"));
		$this->assertTrue($val->validateValue("<ul><li>Hello <li>World</li></ul>"));
		$this->assertFalse($val->validateValue("<p:error>Hello</p:error>"));
		$this->assertFalse($val->validateValue("<error-tag>Hello</p:error>"));
		$this->assertFalse($val->validateValue("<error-tag>Hello</error-tag>"));
	}

	public function testMarkdownValidator() {
		$md = new Tag2BracketGenerator( new TextTagInfo() );
		$val = new IsMarkdownValidator($md);

		$this->assertTrue($val->validateValue("<p>Text<b>bold</b><i>italic</i></p>"));
		$this->assertTrue($val->validateValue("<p>My<br>Text</p>"));
		$this->assertFalse($val->validateValue("<p>My<br>Text</br></p>"));
	}
}
