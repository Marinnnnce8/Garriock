<?php

namespace ProcessWire;

/**
 * Branches (locations) 
 *
 */
$page->hasMap = true;

$locations = $page->children();
$divisions = $pages->find('template=division,include=hidden');
?>
<div pw-replace='page-<?= $page->template->name ?>'>
    <div class="uk-grid-medium uk-child-width-1-2@m uk-child-width-1-3@l" data-uk-grid="">
        <?php foreach ($locations as $location): ?>
            <?php echo renderLocationCard($location); ?>
        <?php endforeach; ?>
    </div>


    <div class="uk-section uk-margin-large-top uk-padding-remove">
        <div class="uk-container">
            <div class="section-header">
                <h2 class="section-title">Find Us On Map</h2>
            </div>
        </div>

<?php        foreach ($divisions->find("lat!=,lng!=") as $d)
                            $markers[$d->id] = renderMarker($d, true);
                       
                       echo         renderMap(
                                        [
                                    "center" => [
                                        "lat" => $d->lat,
                                        "lng" => $d->lng,
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
                                        ]
                                ); ?>
        
    </div>
</div>
