<?php namespace ProcessWire;

/**
 * Post
 *
 */

$before .= $nb->htmlWrap(date("F jS Y", $page->date_pub), "<div class='nb-date uk-text-meta'>");
$content .= nbContent($page);
