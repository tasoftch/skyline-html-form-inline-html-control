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

namespace Skyline\HTML\Form\Control\Text;

use Skyline\HTML\Element;
use Skyline\HTML\ElementInterface;
use Skyline\HTML\Form\Control\AbstractLabelControl;
use Skyline\HTML\Form\Control\DefaultContainerBuilderTrait;
use Skyline\HTML\TextContentElement;

class HTMLEditorControl extends AbstractLabelControl
{
	use DefaultContainerBuilderTrait;

	/** @var int  */
	private $rows = 10;

	/**
	 * @return int
	 */
	public function getRows(): int
	{
		return $this->rows;
	}

	/**
	 * @param int $rows
	 * @return static
	 */
	public function setRows(int $rows)
	{
		$this->rows = $rows;
		return $this;
	}

	protected function buildInitialElement(): ?ElementInterface
	{
		return new Element("div");
	}

	protected function buildControlElementInstance(): ElementInterface
	{
		$e = new TextContentElement("textarea");
		$e->setSkipInlineFormat(true);
		return $e;
	}

	protected function buildControl(): ElementInterface
	{
		/** @var TextContentElement $control */
		$control = parent::buildControl();

		$control->setContent( $control["value"] ?? "" );
		unset($control["value"]);

		$id = $this->getID();

		$properties = json_encode([

		]);

		$control["rows"] = $this->getRows();
		echo "<script type='application/javascript'>(function($){if($){\$(function() {
    if($.fn.pell!==undefined) {
        $('#$id').pell($properties).each(function() {
            this.pell.content.innerHTML = $(this).val().replace(/&lt/g, '<').replace(/&gt;/, '>');
        });
    }else{console.error('Simple inline html editor requires the SkylinePell component')}
})}else console.error('HTML Editor requires jQuery.');})(window.jQuery)</script>";
		return $control;
	}
}