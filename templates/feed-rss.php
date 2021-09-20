<?php namespace ProcessWire;

/**
 * RSS Feed
 *
 */

$posts = $pages->find([
	"template" => "post",
	"date_pub!=" => "",
	"sort" => "-date_pub",
]);

$mostRecent = $posts->first();

if(count($posts)) {

?><?= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" ?>
<rss xmlns:dc="http://purl.org/dc/elements/1.1/"  xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
	<channel>
		<title><?= htmlentities($nb->siteName) ?></title>
		<link><?= $pageHome->httpUrl ?></link>
		<description><?= htmlentities("The latest news from $nb->siteName") ?></description>
		<language>en-gb</language>
		<copyright>Copyright <?= htmlentities($nb->clientName) ?> <?= date("Y") ?>. All rights reserved.</copyright><?php if($mostRecent->id) { ?>
		<pubDate><?= date("r", $mostRecent->date_pub) ?></pubDate>
		<lastBuildDate><?= date("r", $mostRecent->modified) ?></lastBuildDate>
		<?php } else { echo "\n"; } ?>
		<ttl>60</ttl>
		<?php if(file_exists("{$config->paths->templates}img/rss.jpg")): ?>
		<image>
			<link><?= $pageHome->httpUrl ?></link>
			<title><?= htmlentities($nb->siteName) ?></title>
		</image>
		<?php endif; ?>
		<atom:link href="<?= $page->httpUrl ?>" rel="self" type="application/rss+xml" />
		<?php foreach($posts as $post) { ?>
		<item>
			<title><?= $post->title ?></title>
			<link><?= $post->httpUrl ?></link>
			<description><![CDATA[<p><?= nbSummary($post) ?></p>]]></description>
			<guid><?= $post->httpUrl ?></guid>
			<pubDate><?= date("r", $post->date_pub) ?></pubDate>
		</item>
		<?php } ?>
	</channel>
</rss><?php

} else {

	// If no posts, unpublish the feed
	$page->of(false);
		$page->addStatus(Page::statusUnpublished);
	$page->save();
}
