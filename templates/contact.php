<?php namespace ProcessWire;

/**
 * Contact
 *
 */

include("forms/contact.php");

//$content .= nbContent($page);
//$content .= nbBlock("<h3>Contact Form</h3>$form", "form");

?>
<div pw-replace='page-<?= $page->template->name ?>'>
    <div class='uk-section section-locations uk-padding-remove' id="locations">
            <div class='uk-container'>
                
                    <?php
                    foreach ($pageLocations->children() as $p) {

                        $divisions = $pages->find([
                            "template" => "division",
                            "branch" => $p->id,
                            "include" => "hidden",
                        ]);
                        $division = $divisions->first();

                        $markers = [];
                        foreach ($divisions->find("lat!=,lng!=") as $d) {
                            $markers[$d->id] = renderMarker($d, true, false);
                        }
                        $form = buildForm($pages, $nb, $modules, $division);
                        $items[$p->title] = 

                                renderMap(
                                        [
                                    "center" => [
                                        "lat" => $division->lat,
                                        "lng" => $division->lng,
                                    ],
                                    "zoom" => 14,
                                        ], [
                                    "default" => $markers,
                                        ], [
                                    "id" => "map$p->id",
                                    "class" => [
                                        "map-container",
                                        "map-container-small",
                                    ],
                                    "data-nb-gmap-autozoom" => count($markers) > 1,
                                        ]
                                ) .
                                $nb->htmlWrap(
                                    $nb->htmlWrap(
                                        $nb->htmlWrap(    
                                            $nb->htmlWrap(
                                                    '<img src="'.$urls->templates.'img/icon-phone.svg" class="uk-preserve icon-large primary-color" data-uk-svg="">
                                                    <h5>PHONE</h5>
                                                    <div class="label">Our contact phone:</div>' .
                                                    nbTel($division->tel), "<div class='contact-box'>"
                                            ), 'div'
                                        ) .
                                        $nb->htmlWrap(    
                                            $nb->htmlWrap(
                                                '<img src="'.$urls->templates.'img/icon-mail.svg" class="uk-preserve icon-large primary-color" data-uk-svg="">
                                                <h5>EMAIL</h5>
                                                <div class="label">Our contact email:</div>' .
                                                nbMailto($division->email), "<div class='contact-box'>"
                                            ), 'div'
                                        ) .
                                        $nb->htmlWrap(
                                            $nb->htmlWrap(
                                                '<img src="'.$urls->templates.'img/icon-pin.svg" class="uk-preserve icon-large primary-color" data-uk-svg="">
                                                <h5>ADDRESS</h5>
                                                <div class="label">' . $p->title .' office location:</div>' .
                                                '<p>'.nl2br($division->address).' ' . $division->postcode . '</p>', "div"
                                            ), "<div class='contact-box'>"
                                        ), '<div class="uk-flex uk-flex-nowrap uk-child-width-1-3@s uk-grid-small" data-uk-grid="">'
                                        ) . $nb->htmlWrap(
                                            '<h4>Contact the ' . $p->title . ' office</h4>
                                            <p>Fill out the following form with your requirements and one of our team will get back to you as soon as possible.</p>'.$form,
                                            '<div class="contact-form">'
                                    ), '<div class="enquiry-container" data-src="'.$urls->templates.'img/grey-bg-small.jpg" 
                                             data-srcset="'.$urls->templates.'img/grey-bg-small.jpg 1024w, 
                                                          '.$urls->templates.'img/grey-bg-large.jpg 1920w" 
                                             data-sizes="100vw"
                                             data-uk-img="">'
                                );
                    }
                    echo $nb->htmlWrap(
                            ukTabs($items),
                            $nb->renderAttr(
                                    ['id' => 'enquire'],
                                    'div'
                                    )
                            );
                ?>
		</div>
	</div>
</div>