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
use Skyline\HTML\Form\Control\ExportControlInterface;
use Skyline\HTML\Form\Control\ImportControlInterface;
use Skyline\HTML\Form\Control\Text\EditorAction\DefaultAction;
use Skyline\HTML\Form\Control\Text\EditorAction\EditorActionAwareInterface;
use Skyline\HTML\Form\Control\Text\EditorAction\EditorActionInterface;
use Skyline\HTML\Form\Control\Text\EditorAction\ModalAction;
use Skyline\HTML\Form\Markdown\Generator\MarkdownGeneratorInterface;
use Skyline\HTML\TextContentElement;

class HTMLEditorControl extends AbstractLabelControl implements ImportControlInterface, ExportControlInterface
{
	use DefaultContainerBuilderTrait;

	/** @var int  */
	private $rows = 10;

	/** @var MarkdownGeneratorInterface|null */
	private $markdownGenerator;

	/** @var string[]  */
	private $settings = [
		'defaultParagraphSeparatorString' => 'p'
	];

	/** @var string[]|EditorActionAwareInterface[]  */
	private $actions = [];

	/**
	 * @return MarkdownGeneratorInterface|null
	 */
	public function getMarkdownGenerator(): ?MarkdownGeneratorInterface
	{
		return $this->markdownGenerator;
	}

	/**
	 * @param MarkdownGeneratorInterface|null $markdownGenerator
	 * @return static
	 */
	public function setMarkdownGenerator(?MarkdownGeneratorInterface $markdownGenerator)
	{
		$this->markdownGenerator = $markdownGenerator;
		return $this;
	}

	/**
	 * @return EditorActionAwareInterface[]|string[]
	 */
	public function getActions()
	{
		return $this->actions;
	}

	/**
	 * @param EditorActionAwareInterface[]|string[] $actions
	 * @return static
	 */
	public function setActions($actions)
	{
		$this->actions = $actions;
		return $this;
	}

	/**
	 * @param string|EditorActionAwareInterface $action
	 * @return static
	 */
	public function addAction($action) {
		if(is_string($action) && ($a = DefaultAction::getDefaultActions()[strtolower($action)] ?? NULL))
			$this->actions[$action] = $a;
		elseif($action instanceof EditorActionInterface)
			$this->actions[ $action->getName() ] = $action;
		elseif($action instanceof EditorActionAwareInterface && isset( DefaultAction::getDefaultActions()[$action->getName()] ))
			$this->actions[ $action->getName() ] = $action;
		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getSettings(): array
	{
		return $this->settings;
	}

	/**
	 * @param string[] $settings
	 * @return static
	 */
	public function setSettings(array $settings)
	{
		$this->settings = $settings;
		return $this;
	}

	/**
	 * @param $name
	 * @param $value
	 * @return static
	 */
	public function setSetting($name, $value) {
		$this->settings[$name] = $value;
		return $this;
	}

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

	/**
	 * @inheritDoc
	 */
	public function exportValue()
	{
		if($md = $this->getMarkdownGenerator()) {
			return $md->generateFromInput( $this->getValue() );
		}
		return $this->getValue();
	}

	/**
	 * @inheritDoc
	 */
	public function importValue($value): bool
	{
		if($md = $this->getMarkdownGenerator()) {
			$this->setValue( $md->generateInput( $value ) );
			return true;
		}
		return false;
	}

	/**
	 * @inheritDoc
	 */
	protected function buildInitialElement(): ?ElementInterface
	{
		return new Element("div");
	}

	/**
	 * @inheritDoc
	 */
	protected function buildControlElementInstance(): ElementInterface
	{
		$e = new TextContentElement("textarea");
		$e->setSkipInlineFormat(true);
		return $e;
	}

	/**
	 * @inheritDoc
	 */
	protected function buildControl(): ElementInterface
	{
		/** @var TextContentElement $control */
		$control = parent::buildControl();

		$control->setContent( $control["value"] ?? "" );
		unset($control["value"]);

		$id = $this->getID();

		$settings = $this->getSettings();
		/** @var EditorActionInterface $action */
		foreach(($this->getActions() ?: DefaultAction::getDefaultActions()) as $action) {
			$AC = [];
			if($i = $action->getTitle())
				$AC['title'] = $i;
			if($i = $action->getIcon())
				$AC['icon'] = $i;
			if($action instanceof ModalAction) {
				$AC['modal'] = base64_encode( serialize([$action->getStatusHandler(), $action->getActionHandler(), $action->getName()]) );
			}
			elseif($action instanceof EditorActionInterface) {
				if($i = $action->getStatusHandler())
					$AC['state'] = base64_encode( $i );
				$AC['result'] = base64_encode( $action->getActionHandler() );
			}
			if($AC) {
				$AC["name"] = $action->getName();
				$settings['actions'][] = $AC;
			}
			else
				$settings['actions'][] = $action->getName();
		}

		$properties = json_encode( $settings );
		$properties = preg_replace_callback("/\"(state|result)\":\"([^\"]+)\"/i", function($ms) use ($id) {
			return sprintf('"%s": () => { (function(){%s}).call($("#%s")[0].pell) }', $ms[1], base64_decode($ms[2]), $id);
		}, $properties);
		$properties = preg_replace_callback("/\"modal\":\"([^\"]+)\"/i", function($ms) use ($id) {
			list($init, $action, $name) = unserialize( base64_decode($ms[1]) );

			return sprintf('"result": () => { (function(){this.startModal("%s", (selection) => {%s}, (code, ...arguments) => {%s})}).call($("#%s")[0].pell) }', $name, $init, $action,  $id);
		}, $properties);

		$control["rows"] = $this->getRows();
		echo "<script type='application/javascript'>(function($){if($){\$(function() {
    if($.fn.pell!==undefined) {
        $('#$id').pell($properties).each(function() {
            this.pell.content = $(this).val().replace(/&lt/g, '<').replace(/&gt;/, '>');
        });
    }else{console.error('Simple inline html editor requires the SkylinePell component')}
})}else console.error('HTML Editor requires jQuery.');})(window.jQuery)</script>";
		return $control;
	}
}