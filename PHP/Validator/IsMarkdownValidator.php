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

namespace Skyline\HTML\Form\Validator;

use Skyline\HTML\Form\Exception\FormValidationException;
use Skyline\HTML\Form\Exception\MarkdownException;
use Skyline\HTML\Form\Markdown\Generator\MarkdownGeneratorValidatorInterface;

class IsMarkdownValidator extends AbstractValidator
{
	/** @var MarkdownGeneratorValidatorInterface */
	private $markdownGenerator;
	private $forceCreation;

	/**
	 * IsMarkdownValidator constructor.
	 *
	 * If force creation flag is enabled, the markdown generator gets instructed to create the markdown.
	 * If it thrown an exception, the validation fails.
	 *
	 * @param MarkdownGeneratorValidatorInterface $markdownGenerator
	 * @param bool $forceCreation
	 */
	public function __construct(MarkdownGeneratorValidatorInterface $markdownGenerator, bool $forceCreation = false)
	{
		$this->markdownGenerator = $markdownGenerator;
		$this->forceCreation = $forceCreation;
	}

	/**
	 * @inheritDoc
	 */
	public function validateValue($value)
	{
		if($this->forceCreation) {
			try {
				$this->getMarkdownGenerator()->generateFromInput($value);
			} catch (MarkdownException $e) {
				throw new FormValidationException($e->getMessage(), $e->getCode(), $e);
			}
		}
		return $this->getMarkdownGenerator()->canGenerateFromInput($value);
	}

	/**
	 * @return MarkdownGeneratorValidatorInterface
	 */
	public function getMarkdownGenerator(): MarkdownGeneratorValidatorInterface
	{
		return $this->markdownGenerator;
	}
}