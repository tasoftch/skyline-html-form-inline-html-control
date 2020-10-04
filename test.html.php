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

use Skyline\HTML\Form\Control\Button\ActionButtonControl;
use Skyline\HTML\Form\Control\Text\HTMLEditorControl;
use Skyline\HTML\Form\FormElement;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\ChainTagInfo;use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\ListTagInfo;use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\TextTagInfo;use Skyline\HTML\Form\Markdown\Generator\Tag2BracketGenerator;use Skyline\HTML\Form\Validator\IsHTMLValidator;use Skyline\HTML\Form\Validator\IsMarkdownValidator;use Symfony\Component\HttpFoundation\Request;

require "vendor/autoload.php";

    $form = new FormElement("");

    $md = new Tag2BracketGenerator(new ChainTagInfo([
        new TextTagInfo(),
        new ListTagInfo()
    ]));

    $form->appendElement(
        (new HTMLEditorControl("editor", 'editor'))
        ->addValidator(new IsMarkdownValidator($md, true))
    );

    $form->setActionControl(new ActionButtonControl('save', function($data) use ($md) {
        $data["editor"] = $md->generateFromInput($data["editor"]);
        var_dump($data);
    }));

    $form->evaluateWithRequest(Request::createFromGlobals(), function() {
        return [
            'editor' => '<p><b>Hello</b> World!</p>'
        ];
    })
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="dist/skyline-html-control.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" href="dist/skyline-html-control.min.css" type="text/css" media="all">
	<title>Test</title>
</head>
<body>
<?php

$form->manualBuildForm(function() use ($form) {
    ?>
        <div class="m-5">
            <div class="form-group row my-3">
                <label for="editor" class="col-form-label col-md-3">The Code:</label>
                <div class="col-md-9">
					<?php
					$form->manualBuildControl('editor');
					?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-3"><br></div>
                <div class="col-md-9">
                    <button class="btn btn-outline-primary" name="save">
                        Save
                    </button>
                </div>
            </div>
        </div>
    <?php
});

?>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
</body>
</html>