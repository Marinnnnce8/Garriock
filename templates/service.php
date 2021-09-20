<?php

namespace ProcessWire;

/**
 * Service
 *
 */
$page->hasMap = true;

// get the first divisions of each branch
$branches = $pages->find([
    'template' => 'branch',
]);
$offices = [];
if ($branches->count) {
    foreach ($branches as $branch) {
        $divisions = $pages->find([
            "template" => "division",
            "branch" => $branch->id,
            "include" => "hidden",
        ]);
        $offices[] = $divisions->first();
    }
}

$services = $pages->find('template=service');
$servicesHtml = '';
if ($services->count) {
    foreach ($services as $service) {
        if ($service->id != $page->id) {
            $servicesHtml .= "<li><a href='$service->url'>$service->title</a></li>";
        }
    }
    $servicesHtml = $nb->htmlWrap(
                '<h4 class="widget-title">Services:</h4>' . 
                $nb->htmlWrap(
                    $servicesHtml,
                    '<ul class="uk-list">'
                ), '<div class="widget widget-services">'
            );
}

$officesHtml = '';
if (count($offices)) {
    foreach ($offices as $office) {
        $officesHtml .= '<li>'
        . '<div><small>' . $office->title . ':</small></div>'
        . '<i class="fas fa-phone"></i>' . nbTel($office->tel) 
        . '</li>';
    }
}
$talk = 
        '<h4 class="widget-title">Let\'s talk about your project:</h4>'
        . '<ul class="uk-list">' . $officesHtml . '</ul>';

$enquire = $nb->htmlWrap(
        '<a href="'.$urlEnquire.'" class="uk-button uk-button-large uk-button-secondary ghost pull-icon-right">'
        . 'ENQUIRE NOW<i class="fas fa-long-arrow-alt-right"></i>'
        . '</a>',
        'uk-margin-top'
    );

$sidebar = $nb->htmlWrap(
            $nb->htmlWrap(
                $servicesHtml . 
                $nb->htmlWrap(
                        $talk . $enquire
                        ,'<div class="widget widget-talk" data-uk-sticky="media:960; bottom:aside">'
                    ),
                '<div class="sidebar">'
            ), 
        '<aside class="uk-width-1-3@m">');

$plantHire = '';
if ($page->available_plant_type->count) {
    $plantHire = renderAvailablePlant($page->available_plant_type, $pageLocations);
}

$children = $page->children;
$subPagesLinks = $children->count ? renderSubPages($children) : '';

$content .= 
        $nb->htmlWrap(
            $nb->htmlWrap(
                    $nb->htmlWrap(
                            renderIntro($page->intro) 
                            . nbContent($page)
                            . $plantHire
                            . $subPagesLinks,
                            'uk-width-2-3@m')
                    . $sidebar,
                    '<div class="main-grid" data-uk-grid="">'), 
            'uk-container lift-me-up uk-background-default'
        );

$after = renderPrevAndNextBlock($page, 'services');
$related = $pages->find([
    'template' => 'service',
    'limit' => '4',
    'sort' => 'random',
]);
$after .= renderRelatedServices($related);