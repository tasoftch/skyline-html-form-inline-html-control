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


class ModalAction extends AbstractAction
{
	/**
	 * ModalAction constructor.
	 *
	 * The initial handler gets called to initialize a modal. Its handler gets the current selection passed as argument.
	 * The final handler gets called on modal stop. The modal can be stopped by returning a code and a response.
	 * If the code is 0, the final handler does not get called. If its anything else, the final handler gets invoked.
	 *
	 * Please node that SkylinePell use the bootstrap css framework and will run modal with id == action.name.
	 * It will also add a handler so the modal gets really stopped if the modal closes.
	 *
	 * @param string $name
	 * @param string $initialHandler
	 * @param string $finalHandler
	 * @param string|null $title
	 * @param string|null $icon
	 */
	public function __construct(string $name, string $initialHandler, string $finalHandler, string $title = NULL, string $icon = NULL)
	{
		parent::__construct($name, $title, $icon);
		$this->actionHandler = $finalHandler;
		$this->statusHandler = $initialHandler;
	}

	/**
	 * @return string
	 */
	public function getInitialHandler(): string
	{
		return $this->statusHandler;
	}

	/**
	 * @return string
	 */
	public function getFinalHandler(): string
	{
		return $this->actionHandler;
	}

	/**
	 * @param string $initialHandler
	 * @return ModalAction
	 */
	public function setInitialHandler(string $initialHandler): ModalAction
	{
		$this->statusHandler = $initialHandler;
		return $this;
	}

	/**
	 * @param string $finalHandler
	 * @return ModalAction
	 */
	public function setFinalHandler(string $finalHandler): ModalAction
	{
		$this->actionHandler = $finalHandler;
		return $this;
	}
}