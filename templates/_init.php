<?php namespace ProcessWire;

/**
 * Initialize
 *
 * Sets up site functions, defaults, styles and scripts
 *
 */

/**
 * Site functions
 * 
 */
include_once("./_nb.php");
include_once("./_uikit.php");
include_once("./_func.php");

/**
 * Add stylesheets
 * 
 */
foreach([
	"css/uikit.min.css",
	"css/nb.css",
        "css/vendors.css",
	"css/site.css",
	"css/print.css",
] as $style)
	$config->styles->add("{$urls->templates}$style");

/**
 * Add scripts
 * 
 */
foreach([
	"js/modernizr-custom.js",
        "js/uikit.min.js",
	"js/uikit-icons.min.js",
    	"js/nb.js",
	"js/verge.min.js",
        "js/main.js",
        "js/site.js",
        "js/vendors.js",
] as $script)
	$config->scripts->add("{$urls->templates}$script");

/**
 * Homepage
 * 
 */
$pageHome = $pages->get(1);


/**
 * Page
 * 
 */

// Is this?
$page->isHome = $page->id == $pageHome->id;
$page->isArticle = in_array($page->template->name, ["post"]);

// Meta
$page->metaDesc = $page->get("seo_desc|summary|intro");

// Open Graph
$page->ogDesc = $page->get("og_desc|seo_desc|summary|intro");
$page->ogImage = $nb->getImage($page, [
	"height" => 0,
	"width" => 0,
	"fields" => [
		"og_image",
		"thumb",
		"images",
	]
]);

// Canonical URL
$page->urlCanonical = $page->httpUrl;

// Page Title
$page->titlePage = $page->get("headline|title");

// Hero Image - default imagebanner
$page->imageBanner = $page->banner ?: ($page->parent->banner ?: $pageHome->banner);

// Breadcrumbs
$page->breadCrumbs = $pages->newPageArray();
$page->breadCrumbs->import($page->parents);

// Wrap the content? Add template name to array to exclude.
$page->wrapContent = !in_array($page->template->name, [
	"home",
]);

$page->hasMap = false;

/**
 * Variables
 * 
 */

$nb->imageFields[] = 'blocks';

// Default navigation items
//$nb->navItems = $nb->navItems->prepend($pageHome);

// Default UIkit container class
$nb->ukContainer = "small";

// The page content variables
$prepend = ""; // Before main 
$before = ""; // Before content, after title
$content = ""; // Page content
$after = ""; // After content container
$append = ""; // After main

$urlEdinburgh = $pageHome->links[0]->link;
$urlContact = $pages->get(1069)->url;
$urlEnquire = $urlContact;

$pageServices = $pages->get(1064);
$pageStudies = $pages->get(1071);
$pageLocations = $pages->get(1467);
$pageNews = $pages->get(1073);
$pageCategories = $pages->get(1780);

$tplLink = "<li><a href='{url}'>{title}</a></li>";

$fieldsPost = [
	"date_pub" => true,
	"summary" => true,
	"image" => "url",
];

$fieldsStudy = [
	"summary" => true,
	"image" => "url",
	"location" => true,
	"services" => [
		[
			"title" => true,
		],
	],
];
