<?php

use Skyline\Component\Config\AbstractComponent;
use Skyline\Component\Config\CSSComponent;
use Skyline\Component\Config\JavaScriptComponent;

return [
	// all these keys can be required in templates like @require YourComponent
    "SkylinePell" => [
    	// Use the JavaScriptComponent to load the scripts in html head tag, so before any content gets loaded.
		"js" => new JavaScriptComponent(
				...AbstractComponent::makeLocalFileComponentArguments(
				"/Public/Skyline/html-form-inline-html-control.js",
				__DIR__ . "/dist/skyline-html-control.min.js"
			)
		),

        "css" => new CSSComponent(
        	// CSS components are always loaded before the body contents.
			...AbstractComponent::makeLocalFileComponentArguments(
				"/Public/Skyline/html-form-inline-html-control.css",
				__DIR__ . "/dist/skyline-html-control.min.css",
				'sha384',
				NULL,
				'all'
			)
		)
    ]
];