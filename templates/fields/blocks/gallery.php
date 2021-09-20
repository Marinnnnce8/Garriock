<?php namespace ProcessWire;

/**
 * Gallery Block
 *
 */


if($page->gallery->count()) {

	$isFull = $page->checkbox;
	$out = nbBlock(
		($page->title ? $nb->htmlWrap(
			"<h5>$page->title</h5>",
			($isFull ? "<div class='uk-container uk-margin-bottom'>" : "")
		) : "") . 
		nbGallery($page->gallery, []) . 
		($page->headline ? $nb->htmlWrap(
			$page->headline,
			"<div class='uk-text-center uk-margin-small-top'>"
		) : ""),
		"gallery",
		[
			"class" => [
				"gallery-" . ($isFull ? "wide" : "normal"),
			],
		]
	);
	echo $out;
	//echo $isFull ? wrapFull($out) : $out;
}