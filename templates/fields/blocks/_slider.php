<?php namespace ProcessWire;

/**
 * Slider Block
 *
 */

if($page->gallery->count())
	echo nbBlock(
		($page->title ? "<h3>$page->title</h3>" : "") . 
		ukSlider($page->gallery, []),
		"slider"
	);
