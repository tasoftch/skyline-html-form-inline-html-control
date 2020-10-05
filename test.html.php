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
use Skyline\HTML\Form\Control\Text\EditorAction\DefaultAction;
use Skyline\HTML\Form\Control\Text\EditorAction\ModalAction;
use Skyline\HTML\Form\Control\Text\HTMLEditorControl;
use Skyline\HTML\Form\FormElement;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\ChainTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\CodeTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\ImageTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\LineTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\LinkTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\ListTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\TextTagInfo;
use Skyline\HTML\Form\Markdown\Generator\Tag2BracketGenerator;
use Skyline\HTML\Form\Validator\IsMarkdownValidator;
use Symfony\Component\HttpFoundation\Request;
use Skyline\HTML\Form\Markdown\Generator\Tag2Bracket\QuoteTagInfo;

require "vendor/autoload.php";
    // Create a form element without an action (means, it will call the same page to handle the form)
    $form = new FormElement("");

    // Define the markdown generator
    $md = new Tag2BracketGenerator(new ChainTagInfo([
        new TextTagInfo(),
        new ListTagInfo(),
        new LinkTagInfo(),
        new ImageTagInfo(),
        new CodeTagInfo(),
        new LineTagInfo(),
        new QuoteTagInfo()
    ]));

    // Create the editor element
    $form->appendElement(
        (new HTMLEditorControl("editor", 'editor'))
        ->addValidator(new IsMarkdownValidator($md, true))
        ->setMarkdownGenerator($md)
            ->setActions(DefaultAction::getDefaultActions())
        ->addAction(new ModalAction(
                'link',
            '$("#link-label").val( selection ? selection : "" )',
            'if(code) {let url=arguments[0], label=arguments[1], target=arguments[2]; if(target){this.insertHTML("<a title=\""+url+"\" href=\""+url+"\" target=\""+target+"\">"+label+"</a>");}else{this.insertHTML("<a title=\""+url+"\" href=\""+url+"\">"+label+"</a>");} }'
        ))
        ->addAction(new ModalAction(
                'image',
            "",
            'if(code){this.insertHTML("<img src=\""+arguments[0]+"\" alt=\""+arguments[1]+"\" title=\""+arguments[2]+"\">")}'
        ))
    );

    // Set an action to display the transmitted values
    $form->setActionControl(new ActionButtonControl('save', function($data) use ($md) {
        var_dump($data);
    }));

    $form->evaluateWithRequest(Request::createFromGlobals(), [
		'editor' => '[p]Hier [A:href=https%3A%2F%2Ftasoft.ch]bin[/A] ich.    jaja
[/p][p][img:src=skyline.png&alt=Hier+bin+ich&title=Aber+hallO%21][/p][p][A:href=https%3A%2F%2Ftasoft.ch&target=_blank]Nun ist es aber so[/A]
Dass es nicht so weiter geht.[/p]'
    ], function($raw, $fails) {
        $ed = $fails["editor"]->getStoppedValidationReason();
        if($ed instanceof Exception) {
            ?>
            <div class="alert-danger alert m-5">
                <h4 class="alert-heading">Validation failed</h4>
                <p>
                    <?=$ed->getMessage() ?>
                </p>
            </div>
            <?php
        }
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
    <style type="text/css">
        .pell img {
            max-width: 128px;
            max-height: 128px;
        }
    </style>
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

<div class="modal fade" id="link" tabindex="-1" role="dialog" aria-labelledby="link-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="" onsubmit="return SkylinePell.stopModal(1, $('#scheme').val() + $('#link-url').val(), $('#link-label').val(), $('#link-target').val())">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="link-modal-title">Link to Website</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="link-label" class="col-form-label">Label:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="link-label">
                            <div class="input-group-append">
                                <select id="link-target" class="custom-select" aria-label="Target">
                                    <option value="">None</option>
                                    <option value="_blank">New Window</option>
                                    <option value="_parent">Parent Frame</option>
                                    <option value="_self">This Frame</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="link-url" class="col-form-label">URL:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select id="scheme" class="custom-select" aria-label="Scheme">
                                    <option>https://</option>
                                    <option>http://</option>
                                </select>
                            </div>
                            <input type="text" class="form-control" id="link-url">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Link it!</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="image" tabindex="-1" role="dialog" aria-labelledby="image-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="" onsubmit="return SkylinePell.stopModal(1, $('#image-preview').attr('src'), $('#image-caption').val(), $('#image-alt').val())">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="image-modal-title">Link to Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <figure class="figure text-center w-50">
                            <img id="image-preview" class="img-fluid img-thumbnail w-100 rounded shadow" src="skyline.png" alt="" style="min-height: 5rem">
                        </figure>
                    </div>

                    <div class="form-group">
                        <label for="image-url" class="col-form-label">URL:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">@</span>
                            </div>
                            <input type="text" placeholder="skyline.png" class="form-control" id="image-url" onchange="$('#image-preview').attr('src', $(this).val())">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image-caption" class="col-form-label">Caption</label>
                        <input type="text" class="form-control" id="image-caption">
                    </div>

                    <div class="form-group">
                        <label for="image-alt" class="col-form-label">Alternate</label>
                        <input type="text" class="form-control" id="image-alt">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Insert</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
</body>
</html>