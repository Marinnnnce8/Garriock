<?php namespace ProcessWire;

/**
 * Embed Block
 * 
 */

if($page->html)
	echo nbBlock(
		($page->title ? "<h3>$page->title</h3>" : "") . 
		nbIntro($page) . 
		$page->html,
		"embed"
	);
