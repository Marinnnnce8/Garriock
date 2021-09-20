<?php namespace ProcessWire;

/**
 * Services
 *
 */

//$content .= nbContent($page) . 
//	renderRelated($page, [
//		"action" => "services",
//	]);
//
//$page->urlCanonical = $nb->urlCanonicalAll($page);

$services = $page->children();

?>
<div pw-replace='page-<?= $page->template->name ?>'>
    <div class="service-grid uk-grid-small uk-child-width-1-3@s uk-child-width-1-4@l" data-uk-grid="">
    <?php foreach ($services as $service): ?>
        <?php echo renderServiceCard($service, 'uk-background-grey'); ?>
    <?php endforeach; ?>
    </div>
</div>