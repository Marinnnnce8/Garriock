<?php namespace ProcessWire;

/**
 * Homepage
 *
 */
$page->hasMap = true;
$urlAbout = $pages->get(1065)->url;

// forms builder
include 'forms/contact.php';
?>
    <div pw-replace='page-<?= $page->template->name ?>'>
        <div class="hero-section full-screen">
            <div class="hero-inner hero-gradient" data-src="<?= $page->banner->url ?>" data-srcset="<?= $page->banner->url ?> 1024w, <?= $page->banner->url ?> 1920w" data-sizes="100vw" data-uk-img="">
                <div class="uk-container">
                    <div class="vertical-center">
                        <div class="hero-content">
                            <h1 class="uk-heading-hero uk-light"><?= $page->strapline ?></h1>
                            <a href="<?= $urlAbout ?>" class="uk-button uk-button-primary uk-button-large pull-icon-right">FIND OUT MORE ABOUT US<i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="uk-section uk-padding-remove section-featured">
            <div class="uk-container">
                <div class="uk-child-width-1-2@s" data-uk-grid=""> 
                    <?php foreach ($page->featured as $featured): 
                        echo renderFeatured($featured);
                    endforeach;?>
                </div>
            </div>
        </div><!-- .section-featured -->

        <div class="uk-section">
            <div class="uk-container">
                <div class="uk-flex uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-medium" data-uk-grid="" data-uk-height-match="">
                    <div>
                        <p class="uk-text-lead">
                            <strong>
                                <?=$page->intro?>
                            </strong>
                        </p>
                    </div>
                    <div class="last-on-tablet uk-width-1-3@m">
                        <div class="text-box">
                            <?=$page->body?>
                            <a href="<?=$urlAbout?>" class="uk-button uk-button-link">FIND OUT MORE ABOUT US<i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                    <div class="uk-position-relative">
                        <figure class="ad">
                            <img src="<?=$page->ad_img->url?>" alt="<?=$page->ad_img->description?>">
                            <div class="overlay-dark uk-position-cover">
                                <?php if ($page->ad_logo): ?>
                                <img class="ad-logo" src="<?=$page->ad_logo->url?>" alt="<?=$page->ad_logo->desc?>">
                                <?php endif; ?>
                                <div class="uk-position-bottom">
                                    <h3 class="heading"><?=$page->ad_title?></h3>
                                    <p class="uk-text-muted"><?=$page->ad_intro?></p>
                                </div>
                            </div>
                            <figcaption><span><?=$page->ad_link_text?></span><i class="fas fa-external-link-alt"></i></figcaption>
                        </figure>
                         <a class="uk-position-cover" href="<?=$page->ad_link?>" title="<?=$page->ad_link_text?>" target="_blank" rel="noopener noreferrer"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="uk-section uk-background-grey">
            <div class="uk-container">
                <div class="section-header">
                    <h2 class="section-title underlined"><?=$pageServices->get("headline|title")?></h2>
                    <div class="section-summary"><?=nbSummary($pageServices)?></div>
                </div>
                <div class="service-grid uk-grid-small uk-child-width-1-3@s uk-child-width-1-4@l" data-uk-grid="">
                    <?php foreach ($pageServices->children() as $service): 
                        echo renderServiceCard($service);
                    endforeach; ?>
                </div><!-- service-grid -->

                <div class="uk-margin-medium-top uk-text-right">
                    <a href="<?=$pageServices->url?>" class="uk-button uk-button-secondary ghost pull-icon-right">DISCOVER MORE<i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="uk-section">
            <div class="uk-container">

                <div class="section-header">
                    <h2 class="section-title underlined"><?= $pageStudies->get("headline|title") ?></h2>
                    <div class="section-summary"><?= nbSummary($pageStudies) ?></div>
                </div>
                <div class="the-slider uk-position-relative" data-uk-slider="">
                    <div class="uk-slider-container">
                        <ul class="uk-slider-items uk-grid uk-grid-medium uk-child-width-1-2@m">
                            <?php foreach ($pageStudies->children("limit=$nb->limit") as $study): ?>
                            <li>
                                <?php echo renderStudyCard($study); ?>
                            </li>
                            <?php endforeach; ?> 
                        </ul>
                    </div>

                    <a class="uk-position-center-left-out uk-hidden-hover" href="#" data-uk-slidenav-previous data-uk-slider-item="previous"></a>
                    <a class="uk-position-center-right-out uk-hidden-hover" href="#" data-uk-slidenav-next data-uk-slider-item="next"></a>

                    <ul class="uk-slider-nav uk-dotnav uk-flex-center"></ul>
                </div>

                <div class="uk-text-right">
                    <a href="<?= $pageStudies->url ?>" class="uk-button uk-button-secondary ghost uk-button-large pull-icon-right">DISCOVER MORE<i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
            </div>
        </div><!-- .uk-section -->

        <div class='uk-section section-locations' id="locations">
            <div class='uk-container'>
                <div class="section-header">
                    <h2 class="section-title underlined"><?= $pageLocations->get("headline|title") ?></h2>
                    <div class="section-summary"><?= nbSummary($pageLocations) ?></div>
                </div>
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
                                                    '<img alt="phone icon" src="'.$urls->templates.'img/icon-phone.svg" class="uk-preserve icon-large primary-color" data-uk-svg="">
                                                    <h5>PHONE</h5>
                                                    <div class="label">Our contact phone:</div>' .
                                                    nbTel($division->tel), "<div class='contact-box'>"
                                            ), 'div'
                                        ) .
                                        $nb->htmlWrap(    
                                            $nb->htmlWrap(
                                                '<img alt="email icon" src="'.$urls->templates.'img/icon-mail.svg" class="uk-preserve icon-large primary-color" data-uk-svg="">
                                                <h5>EMAIL</h5>
                                                <div class="label">Our contact email:</div>' .
                                                nbMailto($division->email), "<div class='contact-box'>"
                                            ), 'div'
                                        ) .
                                        $nb->htmlWrap(
                                            $nb->htmlWrap(
                                                '<img alt="pin icon" src="'.$urls->templates.'img/icon-pin.svg" class="uk-preserve icon-large primary-color" data-uk-svg="">
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
        
        <div class="uk-section uk-background-grey">
            <div class="uk-container">
                <div class="section-header">
                    <h2 class="section-title underlined"><?= $pageNews->get("headline|title") ?></h2>
                    <div class="section-summary"><?= nbSummary($pageNews) ?></div>
                </div>

                <div class="section-new news-grid uk-grid-match uk-child-width-1-3@l" data-uk-grid="">
                    <?php foreach ($pageNews->children("limit=3") as $new):
                        echo renderNewsCard($new);
                    endforeach; ?>
                </div>
                <div class="uk-margin-medium-top uk-text-right">
                    <a href="<?=$pageNews->url?>" class="uk-button uk-button-secondary ghost uk-button-large pull-icon-right">VIEW ALL OUR NEWS<i class="fas fa-long-arrow-alt-right"></i></a>
                </div>
            </div>
        </div><!-- .uk-section -->
    </div>