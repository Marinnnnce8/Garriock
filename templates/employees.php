<?php namespace ProcessWire;

/**
 * Staff Members
 *
 */

$people = $page->children("include=hidden");

$out = "";
if($people->count) {

	foreach($people as $p) {
		$out .= $nb->htmlWrap(
                        renderPersonnelCard($p, true),
                        'uk-width-1-1');
        }
        $out = $nb->htmlWrap(
                $out,
                '<div class="uk-child-width-expand@s" data-uk-grid>'
                );
}

//

$content .= nbContent($page);
$content .= nbBlock($out, "content");
