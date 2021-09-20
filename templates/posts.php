<?php namespace ProcessWire;

/**
 * Posts
 *
 */

if($config->ajax) {

	$start = $input->get->int("start") ?: 0;

	$selectors = [
		"limit" => $nb->limit,
		"start" => $start,
	];

        $children = $page->children($selectors);
        
        foreach ($children as $child) {
            // make sure children have a summary
            $child->summary = nbSummary($child);
            
            // get first image from article
            $child->image = 'truc';
        }
        
        
	
        $nb->returnJSON(
		$children,
		$start,
		[
			"fields" => $fieldsStudy,
			"output" => true,
		]
	);
}

$page->urlCanonical = $nb->urlCanonicalAll($page);
$content .= $nb->getJSON();
