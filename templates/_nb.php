<?php namespace ProcessWire;

/**
 * NB Functions
 *
 */

/**
 * Render a data-nb-date element
 *
 * ~~~~~
 * // Echo the date
 * echo nbDate($page->date_pub);
 * ~~~~~
 *
 * @return string
 * @todo add to js
 *
 */
function nbDate($data = []) {

	if(!is_array($data))
		$data = [
			"start" => $data,
		];

	return nb()->renderAttr([
		"data-nb-date" => array_merge([
			"start" => time(),
			"end" => 0,
			"options" => [],
		], $data)
	], "span", true);
}

/**
 * Render a formatted page introduction
 *
 * ~~~~~
 * // Output the page intro
 * echo nbIntro($page);
 * ~~~~~
 *
 * @param Page $page The Page to be queried
 * @param array $options Options to modify behaviour
 * @return string
 *
 */
function nbIntro(Page $page, array $options = []) {
	return nb()->getIntro($page, $options);
}

/**
 * Renders the Page content
 *
 * This function assumes that a Page will have either
 * a "blocks" or "body" field present. It isn't looking anywhere else.
 *
 * ~~~~~
 * // Render the page content
 * echo nbContent($page);
 * ~~~~~
 *
 * @param Page $page The Page to be queried
 * @return string
 *
 */
function nbContent(Page $page) {
	return nb()->getContent($page);
}

/**
 * Renders a gallery block
 *
 * This function evalues the number of images and the perRow number
 * specified, to return the 'best fit'. For example, if there are
 * 4 images, and perRow is set to 3, the first row will be a single image,
 * resized by width. The next row will be the remaining 3, resized by
 * height, allowing them to be displayed side-by-side without unnecessary whitespace.
 *
 * ~~~~~
 * // Display a gallery of square images, four per row
 * echo nbGallery($page->gallery, [
 *     "height" => 480,
 *     "width" => 480,
 *     "perRow" => 4,
 * ]);
 * ~~~~~
 *
 * @param Pageimages $images The images to be displayed
 * @param array $options Options to modify behavior:
 * - `height` (int): Crop height (default=`$nb->height`)
 * - `lightbox` (array): UIkit Lightbox options
 * Please refer to the uk-lightbox documentation for all available options: https://getuikit.com/docs/lightbox#component-options
 * - `perRow` (int): Number of images per row (default=3)
 * - `width` (int): Crop width (default=`$nb->width`)
 * @return string
 *
 */
function nbGallery(Pageimages $images, array $options = []) {

	$nb = nb();
        
	// Set default options
	$options = array_merge([
		"height" => $nb->height,
		"lightbox" => [],
		"perRow" => 3,
		"width" => $nb->width,
	], $options);

	// Set default lightbox options
	$lightbox = array_merge([
		"animation" => "fade",
	], $options["lightbox"]);

	$c = count($images);
	$out = "";
	if($c) {

		// Get our increment value
		$remainder = $c % $options["perRow"];
		$increment = $remainder ? $remainder : $options["perRow"];

		// Cycle through images and create our gallery rows
		for($y = 0; $y < $c; $y += $increment) {

			if($y == $increment)
				$increment = $options["perRow"];

			$items = "";
			for($x = 0; $x < $increment; $x++) {

				$index = $x + $y;
				$img = $images->eq($index);

				if($img) {

					$thumb =($remainder == 1 && $y == 0 ? $img->width($options["width"]) : $img->height($options["height"]));
					$desc = sanitizer()->entitiesMarkdown($img->description);

					// If a single image and a description has been specified
					// If the image doesn't already have a description
					// Set the image description to the specified description
					if(array_key_exists("desc", $options) && ($y + $x + $c) == 1 && empty($desc))
						$desc = $options["desc"];

					$items .= $nb->htmlWrap(

						$nb->renderAttr([
							"src" => $nb->pixel,
							"alt" => $desc,
							"width" => $thumb->width,
							"height" => $thumb->height,
							"data-uk-img" => true,
							"data-src" => $thumb->url,
						], "img") . 
                                                '<span class="on-hover">
                                                    <i class="icon-zoom-in"></i>
                                                </span>',

						$nb->renderAttr([
							"href" => $img->url,
							"class" => [
								"nb-gallery-image",
								"nb-zoom-in",
							],
							"data-caption" => $desc,
						], "a")
					);
				}
			}

			$out .= $nb->htmlWrap($items, "<div class='nb-gallery-row gallery-grid'>");
		}

		// Render the gallery block
		$out = $nb->htmlWrap($out, $nb->renderAttr([
			"class" => "", //"nb-gallery-images", 
			"data-uk-lightbox" => count($lightbox) ? json_encode($lightbox) : true,
		], "div"));
	}

	return $out;
}

/**
 * Renders a list of files
 *
 * ~~~~~
 * // Render the files, but without the filesize string
 * echo nbFiles($page->files, [
 *     "size" => false,
 * ]);
 * ~~~~~
 *
 * @param Pagefiles $files The files to be rendered
 * @param array $options Options to modify behaviour:
 * - `attributes` (array): An array of attributes to be added to the `<ul>` element
 * - `download` (bool): Force download of the files
 * - `size` (bool): Append the filesize
 * @return string
 *
 * @todo Requires testing, and perhaps default styling
 * @todo target=_blank attribute?
 *
 */
function nbFiles(Pagefiles $files, array $options = []) {

	$nb = nb();

	// Set default options
	$options = array_merge([
		"attr" => [
			"class" => [
				"nb-file-list"
			],
		],
		"download" => true,
		"size" => true,
	], $options);

	$items = [];
	foreach($files as $file)
		$items[] = $nb->htmlWrap(
			($file->description ? $file->description : $file->basename) . 
			($options["size"] ? " <small>($file->filesizeStr)</small>" : ""),
			$nb->renderAttr([
				"href" => $file->url,
				"download" => $options["download"],
				"target" => ($options["download"] ? false : "_blank"),
			], "a")
		);

	return count($files) ? $nb->htmlWrap(
		$nb->htmlWrap($items, "li"),
		$nb->renderAttr($options["attr"], "ul")
	) : "";
}

/**
 * Returns a page description to be used for listings
 *
 * ~~~~~
 * // Output the page summary
 * echo nbSummary($page);
 * ~~~~~
 *
 * @param Page $page The Page to be processed
 * @param array $fields An array of field names that should be checked for values in order of preference
 * @param integer $characters max number of characters
 * @return string
 *
 */
function nbSummary(Page $page, array $fields = ["summary", "intro", "meta_desc"], $characters = 0) {
	$summary = wire("sanitizer")->entities($page->get(implode("|", $fields)));

        // if no summary found, pull from page content
        if (!strlen($summary)) {
            if (!$page->blocks) 
                return '';
            foreach ($page->blocks as $block) {
                if ('text' == $block->type) {
                    $summary = strip_tags($block->body);
                    break;
                }
            }
            $characters = 120;
        }
        
        if ($characters && strlen($summary)) {
            $summary = nbTruncateString($summary, $characters) . '...';
        }
        
        return $summary;
}

function nbTruncateString($string, $characters) {
    
  $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
  $partsCount = count($parts);

  $length = 0;
  $lastPart = 0;
  for (; $lastPart < $partsCount; ++$lastPart) {
    $length += strlen($parts[$lastPart]);
    if ($length > $characters) { break; }
  }

  return implode(array_slice($parts, 0, $lastPart));
}

/**
 * Wraps a string in a nb-block
 *
 * ~~~~~
 * // Output the $page content wrapped in a block
 * echo nbBlock($page->body, "content");
 * ~~~~~
 *
 * @param string $str The string to be wrapped
 * @param string $type The block type (default="content")
 * @param array $attr An array of attributes for the block
 * @return string
 *
 */
function nbBlock($str = "", $type = "content", array $attr = []) {

	$attr = array_merge([
		"class" => [],
		"data-nb-block" => $type,
	], $attr);

	$attr["class"] = array_merge([
		"nb-block",
		"nb-$type",
	], $attr["class"]);

	return nb()->htmlWrap(
		$str,
		nb()->renderAttr($attr, "div")
	);
}

/**
 * Renders the email address as a jQuery obfuscated mailto link
 *
 * ~~~~~
 * // Output a mailto link, with a Font Awesome icon
 * echo nbMailto("tester@nbcommunication.com", [
 *     "icon" => "fa-envelope",
 * );
 * ~~~~~
 *
 * #pw-group-rendering
 *
 * @param string $email The email address
 * @param array $data Other data to be processed by javascript
 * @param array $attr Other attributes to be rendered
 * @return string
 *
 */
function nbMailto($email = "", array $data = [], array $attr = []) {

	$e = explode("@", $email);

	return count($e) == 2 ? nb()->renderAttr(array_merge($attr, [
		"data-nb-mailto" => json_encode(array_merge($data, [
			"id" => $e[0],
			"domain" => $e[1],
		])),
	]), "a") . "</a>" : "";
}

/**
 * Renders a phone number as a tel link
 *
 * It doesn't actually return a 'href' link, but a data-nb-tel one, which
 * the `$nb` javascript turns into a `href='tel:{tel}'` link. This is to prevent
 * scrapers from harvesting numbers from tel: href values.
 *
 * ~~~~~
 * // Output the client's telephone number as a mailto link, with an icon
 * echo nbTel($nb->clientTel, [
 *     "icon" => fa-phone",
 * ]);
 * ~~~~~
 *
 * @param string $tel The telephone number
 * @param array $data Other data to be processed by javascript
 * @param array $attr Other attributes to be rendered
 * @return string
 *
 */
function nbTel($tel = "", array $data = [], array $attr = []) {

	return $tel ? nb()->renderAttr(array_merge($attr, [
		"data-nb-tel" => json_encode(array_merge($data, [
			"tel" => $tel,
		"href" => nb()->formatTelHref($tel),
		])),
	]), "a", true) : "";
}

/**
 * Returns a url with or without the protocol
 *
 * This is primarily for returning urls without http:// or https://.
 * If protocol is set to true, then the url is only run through $sanitizer->url().
 *
 * ~~~~~
 * // Output NB Communication's URL without the protocol
 * echo nbUrl("https://www.nbcommunication.com/");
 * ~~~~~
 *
 * @param string $url The URL to be processed
 * @param bool $protocol Should the protocol be displayed
 * @return string
 *
 */
function nbUrl($url = "", $protocol = false) {

	$url = wire("sanitizer")->url($url);

	return $protocol ? $url : trim(str_replace([
		"https",
		"http",
	], "", str_replace("://", "", $url)), "/");
}

/**
 * Renders the site copyright message
 *
 * A launch date should be set in Setup > Site
 *
 * @return string
 *
 */
function nbCopyright() {

	$nb = nb();

	$thisYear = date("Y");
	$year = $nb->launchDate ? date("Y", $nb->launchDate) : $thisYear;
	$year = date("Y") !== $year ? "$year - " : "";

	return "Copyright &copy; {$year}{$thisYear} {$nb->clientName}. All rights reserved.";
}

/**
 * Renders the NB watermark
 *
 * @param string $img The image to be used
 * @return string
 *
 */
function nbWatermark($img = "nb.png") {

	$nb = nb();

	return $nb->htmlWrap(
		"Website by " .
		$nb->htmlWrap(
			"NB " .
			$nb->renderAttr([
				"src" => $nb->pixel,
				"alt" => "NB Communication Ltd Logo",
				"data-src" => $nb::urlBrand . "logo/web/{$img}",
				"data-uk-img" => true,
			], "img"),
			$nb->renderAttr([
				"href" => "https://www.nbcommunication.com/",
				"title" => "NB Communication - Digital Marketing Agency",
			], "a")
		),
		$nb->renderAttr(["class" => "nb-credit"], "div")
	);
}
