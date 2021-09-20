<?php namespace ProcessWire;

/**
 * Case Studies
 *
 */

if($config->ajax) {

	$start = $input->get->int("start") ?: 0;
        
	$selectors = [
		"limit" => $nb->limit,
		"start" => $start,
	];

        $items = $page->children($selectors);
        
	$nb->returnJSON(
		$items,
		$start,
		[
			"fields" => $fieldsStudy,
			"output" => true,
		]
	);
}

$page->urlCanonical = $nb->urlCanonicalAll($page);
$content .= $nb->getJSON();
