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


class ChainTagInfo implements TagInfoInterface
{
	/** @var TagInfoInterface[] */
	protected $childTagInfos = [];

	/**
	 * ChainTagInfo constructor.
	 * @param TagInfoInterface[] $childTagInfos
	 */
	public function __construct(array $childTagInfos = [])
	{
		$this->childTagInfos = $childTagInfos;
	}

	public function hasTagName(string $tagName, int $options): bool
	{
		foreach($this->getChildTagInfos() as $info) {
			if($info->hasTagName($tagName, $options))
				return true;
		}
		return false;
	}


	/**
	 * @inheritDoc
	 */
	public function getTagInfo(string $tag, int $options): ?string
	{
		foreach($this->getChildTagInfos() as $info) {
			if(!is_null($v = $info->getTagInfo($tag, $options)))
				return $v;
		}
		return NULL;
	}

	/**
	 * @return TagInfoInterface[]
	 */
	public function getChildTagInfos(): array
	{
		return $this->childTagInfos;
	}

	/**
	 * @param TagInfoInterface[] $childTagInfos
	 * @return static
	 */
	public function setChildTagInfos(array $childTagInfos)
	{
		$this->childTagInfos = $childTagInfos;
		return $this;
	}

	/**
	 * @param TagInfoInterface $info
	 * @return static
	 */
	public function addChildTagInfo(TagInfoInterface $info) {
		$this->childTagInfos[] = $info;
		return $this;
	}
}