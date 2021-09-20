<?php namespace ProcessWire;

/**
 * head.php
 *
 * Please retain this code, integrating as necessary
 *
 */

?><!doctype html>
<html lang='en-gb'>
<head>

<?php if(!$nb->siteLive): ?>
	<meta name='robots' content='noindex, nofollow'>
<?php endif; ?>

	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
	<meta name='format-detection' content='telephone=no'>

	<title><?= $page->metaTitle ?></title>

	<meta name='description' content='<?= $sanitizer->entities1($page->metaDesc, true)?>'>
	<meta property='og:title' content='<?= $sanitizer->entities1($page->og_title ?: $page->metaTitle, true) ?>'>
	<meta property='og:description' content='<?= $sanitizer->entities1($page->ogDesc, true) ?>'>
	<meta property='og:url' content='<?= $page->httpUrl ?>'>
	<meta property='og:site_name' content='<?= $nb->siteName ?>'>

<?php if($page->isArticle): ?>
	<meta property='og:type' content='article'>
	<meta property='article:published_time' content='<?= date("Y-m-d H:i:s", $page->get("date_pub|published")) ?>'>
	<meta property='article:modified_time' content='<?= date("Y-m-d H:i:s", $page->modified) ?>'>
<?php else: ?>
	<meta property='og:type' content='website'>
<?php endif; ?>

<?php if($page->ogImage): ?>
	<meta property='og:image' content='<?= $page->ogImage->httpUrl ?>'>
	<meta property='og:image:width' content='<?= $page->ogImage->width ?>'>
	<meta property='og:image:height' content='<?= $page->ogImage->height ?>'>
<?php endif; ?>

	<link rel='canonical' href='<?= $page->urlCanonical ?>'>
	<link rel='shortcut icon' href='<?= $urls->root ?>favicon.ico'><?php

	$pageRSS = $pages->get("template=feed-rss");
	if($pageRSS->id && !$pageRSS->isUnpublished())
		echo "<link href='$pageRSS->url' rel='alternate' type='application/rss+xml' title='$nb->siteName RSS Feed'>";

	foreach($config->styles as $style)
		echo "<link rel='stylesheet' href='$style'>";

	?>

	<script src='https://code.jquery.com/jquery-3.3.1.min.js' integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=' crossorigin='anonymous'></script>
	<script defer src='https://use.fontawesome.com/releases/v5.7.2/js/all.js' integrity='sha384-0pzryjIRos8mFBWMzSSZApWtPl/5++eIfzYmTgBBmXYdhvxPc+XcFEk+zJwDgWbP' crossorigin='anonymous'></script>

<?php if($nb->googleAnalyticsID && $nb->siteLive): ?>
	<script async src='https://www.googletagmanager.com/gtag/js?id=<?= $nb->googleAnalyticsID ?>'></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag("js", new Date());

		gtag("config", "<?= $nb->googleAnalyticsID ?>");
	</script>
<?php endif; ?>
</head>
