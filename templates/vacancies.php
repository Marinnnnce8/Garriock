<?php namespace ProcessWire;

/**
 * Vacancies
 *
 */

$vacancies = $page->children([
	"include" => "hidden",
	"sort" => "date_pub",
]);

$out = "";
if($vacancies->count()) {

	$out .= "<h2>Current Vacancies</h2>";

	foreach($vacancies as $p)
		$out .= $nb->htmlWrap(

			"<h3>$p->title</h3>" . 

			$p->body . 

			($p->files->count() ? nbFiles($p->files) : "") . 

			($p->date_unpub ? $nb->htmlWrap(
				"Please note the closing date for applications is " . date("l jS F", $p->date_unpub) . " at " . date("g:ia", $p->date_unpub),
				"<p class='uk-text-danger uk-text-bold'>"
			) : ""),

			"<div class='uk-placeholder uk-padding-small'>"
		);

} else {

	$out .= ukAlert(
		$nb->htmlWrap(
			"Sorry, there are currently no vacancies. Please check back soon.",
			"<div class='uk-text-center'>"
		),
		"danger"
	);
}

//

$content .= nbContent($page);
$content .= nbBlock($out, "content");
