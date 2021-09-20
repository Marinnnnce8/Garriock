<?php namespace ProcessWire;

/**
 * Content Block
 * 
 */

if($page->body)
	echo $nb->htmlWrap(
                    $nb->htmlWrap(
                        nbBlock(
                        $page->body,
                        "content"
                        ), 
                        'uk-margin-large-bottom content'
                    ), 'uk-container uk-container-small'
            );
