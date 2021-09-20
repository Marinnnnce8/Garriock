<?php namespace ProcessWire;

/**
 * Sitemap
 *
 */

$content .= nbBlock(ukNav($pageHome, [
	"attr" => [
		"class" => [
			"nb-sitemap",
		],
		"data-uk-nav" => false,
	],
]), "content");
