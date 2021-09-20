<?php namespace ProcessWire;

/**
 * Search
 *
 */

$q = $input->get->selectorValue("q");
$input->whitelist("q", $q);

// Templates to find
$findTemplates = [
	"default",
	"post",
	"contact",
	"services",
	"service",
	"studies",
	"study",
	"vacancies",
	"employees",
	"branch",
	"branches",
];

if($config->ajax) {

	$matches = $pages->newPageArray();
	if($q) {

		$start = $input->get->int("start") ?: 0;

		$selectors = [
			"limit" => $nb->limit,
			"start" => $start,
		];

		// Exact Matches
		foreach($pages->find([
			"title|headline%=" => $q,
			"has_parent!=" => 2,
			"template" => $findTemplates,
		]) as $exact)
			if(!$matches->has($exact))
				$matches->add($exact);

		// Content Matches
		foreach($pages->find([
			"intro|summary|body|blocks.body%=" => $q,
			"has_parent!=" => 2,
			"template" => $findTemplates,
		]) as $fuzzy)
			if(!$matches->has($fuzzy))
				$matches->add($fuzzy);

		$matches = $matches->find($selectors);
	}

	$nb->returnJSON($matches, $start, [
		"fields" => [
			"summary" => true,
			"image" => "url",
		],
		"message" => true,
		"noResults" => "Sorry, no results were found.",
		"output" => true,
	]);
}

$page->urlCanonical = $nb->urlCanonicalAll($page);

//

$content .= $nb->getJSON();
