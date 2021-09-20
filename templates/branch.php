<?php

namespace ProcessWire;

/**
 * Location (branch)
 *
 */
$page->hasMap = true;

$divisions = $pages->find([
                            "template" => "division",
                            "branch" => $page->id,
                            "include" => "hidden",
                        ]);
                        $division = $divisions->first();

$locations = $pages->find('template=branch');
$locationsHtml = '';
if ($locations->count) {
    foreach ($locations as $location) {
        if ($location->id != $page->id) {
            $locationsHtml .= "<li><a href='$location->url'>$location->title</a></li>";
        }
    }
    $locationsHtml = $nb->htmlWrap(
                '<h4 class="widget-title">Locations:</h4>' . 
                $nb->htmlWrap(
                    $locationsHtml,
                    '<ul class="uk-list">'
                ), '<div class="widget widget-services">'
            );
}

// facebook link?
$fbLinks = '';
if ('' !== $division->division_facebook) {
    $fbLinksArray = $nb->textToArray($division->division_facebook, ['delimiter' => '=']);
    foreach ($fbLinksArray as $text => $link) {
        $url = $sanitizer->url($link); 
        $fbLinks .= 
            $nb->htmlWrap(
                '<i class="fab fa-facebook-f"></i>' . 
                $nb->htmlWrap(
                    $text,
                    $nb->renderAttr([
                        'href' => $sanitizer->entities($url),
                    ], 'a')
                ), 'li');
    }
}

$divisionLink = $sanitizer->url($division->link);
$talk = 
        '<h4 class="widget-title">Let\'s talk about your project:</h4>'
        . '<p>' . $page->title . ' office: <br />'
        . nl2br($division->address) . '  ' . $division->postcode . '</p>'
        . '<ul class="uk-list">
                <li><i class="fas fa-phone"></i>' . nbTel($division->tel) . '</li>
                <li><i class="fas fa-fax"></i>' . nbTel($division->fax) . '</li>
                <li><i class="fas fa-envelope"></i>' . nbMailto($division->email) . '</li>
                <li><i class="fas fa-globe"></i><a href="'.$sanitizer->entities($divisionLink).'">'.$sanitizer->entities($divisionLink).'</a></li>
                ' . $fbLinks . '
           </ul>';
        

$markers = [renderMarker($division)];
$map = renderMap([
    "center" => [
        "lat" => $division->lat,
        "lng" => $division->lng,
    ],
    "zoom" => 14,
        ], [
    "default" => $markers,
        ], [
    "id" => "map_locations",
    "class" => [
        "map-container",
    //"map-container-small",
    ],
    "data-nb-gmap-autozoom" => count($markers) > 1,
        ]);

$enquire = $nb->htmlWrap(
        '<a href="'.$urlEnquire.'" class="uk-button uk-button-large uk-button-secondary ghost pull-icon-right">'
        . 'ENQUIRE NOW<i class="fas fa-long-arrow-alt-right"></i>'
        . '</a>',
        'uk-margin-top'
    );

$sidebar = $nb->htmlWrap(
            $nb->htmlWrap(
                $locationsHtml . 
                $nb->htmlWrap(
                        $talk . $map . $enquire
                        ,'<div class="widget widget-talk" data-uk-sticky="media:960; bottom:aside">'
                    ),
                '<div class="sidebar">'
            ), 
        '<aside class="uk-width-1-3@m">');

$keyPersonnelHtml = '';
$personnel = $pages->find([
    'template' => 'employee', 
    'include' => 'hidden',
    'branch' => $page->id,
]);
if ($personnel->count) {
    foreach ($personnel as $p) {
        $keyPersonnelHtml .= renderPersonnelCard($p);
    }
    $keyPersonnelHtml = $nb->htmlWrap(
        $nb->htmlWrap(
                '<h3 class="section-title">Key Personnel</h3>',
                '<div class="section-header">'
            )
        . $nb->htmlWrap(
                $keyPersonnelHtml,
                '<div class="service-grid uk-grid-small uk-child-width-1-3@l uk-child-width-1-2@s" data-uk-grid="">'),
        'uk-section uk-padding-remove-bottom'
    ); 
}

$servicesHtml = '';
if ($page->services->count || $page->location_other_services->count) {
    foreach ($page->services as $service) {
        $servicesHtml .= renderServiceCard($service);
    }
    foreach ($page->location_other_services as $other) {
        $servicesHtml .= renderServiceCard($other, 'uk-background-default', true);
    }
    $servicesHtml = 
            $nb->htmlWrap(
                $nb->htmlWrap(
                    $nb->htmlWrap(
                        '<h3 class="section-title underlined">'. $page->title .' Branch Services</h3>',
                        '<div class="section-header">'
                    ) .
                    $nb->htmlWrap(
                        $servicesHtml,
                        '<div class="uk-grid-small service-grid uk-child-width-1-3@s uk-child-width-1-4@l" data-uk-grid>'
                    ),
                'uk-container'),
            '<div class="uk-section uk-background-grey z0">');
}

$studiesHtml = '';
// get 2 studies in this branch
$studies = $pages->find([
        'template' => 'study',
        'branch' => $page->id,
        'limit' => 2,
        'sort' => 'random',
    ]);
if ($studies->count) {
    foreach ($studies as $study) {
        $studiesHtml .= $nb->htmlWrap(
                renderStudyCard($study),
                'div');
    }
    $studiesHtml = $nb->htmlWrap(
            $nb->htmlWrap(
                    $nb->htmlWrap(
                            '<h2 class="section-header underlined">' . $page->title . ' Case Studies</h2>',
                            '<div class="section-header">'
                            ) . 
                    $nb->htmlWrap(
                            $studiesHtml,
                            '<div class="uk-grid-40 uk-child-width-1-2@m" data-uk-grid="">'
                            ),
                    'uk-container'
                    )
            , 'uk-section z0 uk-padding-remove-bottom');
}

$content .= 
        $nb->htmlWrap(
            $nb->htmlWrap(
                    $nb->htmlWrap(
                            renderIntro($page->intro) 
                            . nbContent($page) 
                            . $keyPersonnelHtml,
                            'uk-width-2-3@m')
                    . $sidebar,
                    '<div class="main-grid" data-uk-grid="">'), 
            'uk-container lift-me-up uk-background-default'
        ) . 
        $servicesHtml
        . $studiesHtml ;
