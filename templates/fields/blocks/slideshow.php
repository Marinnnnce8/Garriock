<?php namespace ProcessWire;

/**
 * Slideshow Block
 * 
 */

if($page->gallery->count())
	echo nbBlock(
		($page->title ? "<h3>$page->title</h3>" : "") . 
		ukSlideshow($page->gallery, []),
		"slideshow"
	);

