<?php namespace ProcessWire;

/**
 * Equipment sales
 *
 */

$config->scripts->add("{$urls->templates}js/equipment-filters.js");
$config->styles->add("{$urls->templates}css/equipment-sales.css");

$categories = getEquipmentCategories($pageCategories->children);
$locations = getEquipmentLocations($pageLocations->children);

$categoryFilter = $input->get->pageName('category');
$locationFilter = $input->get->pageName('location');

// filters
$before = $nb->htmlWrap('Filter by category', 'uk-text-uppercase uk-text-bold', 'span') . $categories;
$before .= $nb->htmlWrap('Filter by location', 'uk-text-uppercase uk-text-bold', 'span') . $locations;

//$content .= $nb->htmlWrap('Items', 'h2');



if($config->ajax) {
        $categoryFilter = $input->get('categoryButton') ?: false;
        $locationFilter = $input->get('locationButton') ?: false;
        
	$start = $input->get->int("start") ?: 0;

	$selectors = [
		"limit" => $nb->limit,
		"start" => $start,
	];
        if ($categoryFilter) {
            array_push($selectors, ['category' => $categoryFilter]);
        }
        if ($locationFilter) {
            array_push($selectors, ['equipment_location' => $locationFilter]);
        }
        $children = $page->children($selectors);
        
        $nb->returnJSON(
		$children,
		$start,
		[
			"fields" => [
                            'intro' => true,
                            'headline' => true,
                            'duration' => true,
                            'equipment_id' => true,
                            'gallery' => true,
                            'equipment_location' => 'title',
                            'equipment_price' => true,
                            'equipment_condition' => true,
                            'category' => [
                                'category' => 'title',
                                'icon' => 'url'
                                ],
                            'contact' => [
                                'email' => true,
                            ],
                            'year' => true,
                        ],
			"output" => true,
		]
	);
}

$page->urlCanonical = $nb->urlCanonicalAll($page);
$content .= $nb->getJSON();
