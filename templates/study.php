<?php namespace ProcessWire;

/**
 * Case Study
 *
 */

$rows = [];
foreach([
	"location",
	"client",
	"value",
	"start_date",
	"duration",
] as $f) {

	$v = $page->get($f);
	if($v)
		$rows[] = [
			$page->getField($f)->label,
			($f == "value" ? $nb->formatMoney($v) : $v),
		];
}

if($page->services->count())
	$rows[] = [
		"Services Provided",
		$page->services->implode(", ", "title"),
	];

if(count($rows))
    $before .= renderStudyHighlights($rows, $page, $pages);


if ($page->intro) {
    $content .= renderStudyIntro($page->intro, 'Project Background');
}

$content .= nbContent($page);

// share buttons
$content .= renderShareButtons($modules->MarkupSocialShareButtons);

// prev / next studies
$after = renderPrevAndNextBlock($page);

// related cases
// find 4 cases related to the same service as this page
$services = $page->services;
$related = $pages->find("template=study, limit=4, services=$services, id!=$page->id");

$after .= renderRelatedStudies($related);