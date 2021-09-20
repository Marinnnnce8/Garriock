<?php namespace ProcessWire;

/**
 * Accordion Block
 *
 */

if($page->items->count())
	echo nbBlock(
		($page->title ? "<h3>$page->title</h3>" : "") . 
		ukAccordion($page->items),
		"content"
	);
