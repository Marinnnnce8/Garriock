<?php namespace ProcessWire;

/**
 * Styleguide
 *
 */

include("forms/contact.php");

$content .= nbContent($page);
$content .= nbBlock("<h3>Contact Form</h3>$form", "form");
