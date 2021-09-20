<?php namespace ProcessWire;

/**
 * Site Functions
 *
 */

function renderMap(array $options = [], array $markers = [], array $attr = []) {

	$page = wire("page");

	$options = array_merge([
		"center" => [
			"lat" => 57.506123,
			"lng" => -4.452215,
		],
		"zoom" => 5,
		"scrollwheel" => false,
	], $options);

	$attr = array_merge([
		"id" => "map$page->id",
		"class" => [
			"nb-responsive",
		],
	], $attr);

	$page->hasMap = true;

	return nb()->renderAttr(array_merge($attr, [
		"data-nb-gmap" => [
			"options" => $options,
			"markers" => $markers,
		],
	]), "div", true);
}

function renderMarker($page, $window = false, $displayBranch = true, $marker = "marker.png") {

	$m = [
		"position" => [
			"lat" => $page->lat,
			"lng" => $page->lng,
		],
		"icon" => wire("config")->urls->templates . "img/$marker",
	];

	if($window)
		$m["window"] = [
			"content" => nb()->htmlWrap(
                                ($page->thumb ? "<img src=\"{$page->thumb->width(200)->url}\" alt=\"\">" : "") . 
				'<h3 class="uk-margin-remove">' . $page->title . '</h3>' . 
                                ($displayBranch ? '<p><a href="' . $page->branch->first->url . '">'. $page->branch->first->title .'</a></p>' : '') .
                                '<ul class="uk-list">' .
                                '<li><p>' . nl2br ($page->address) .' '. $page->postcode . '</p></li>' .
                                (!$page->email ? '' : '<li><a href="mailto: '. $page->email .'"><i class="fas fa-envelope"></i> '. $page->email .'</a></li>') .
                                (!$page->tel ? '' : '<li><a href="tel: '.$page->tel.'"><i class="fas fa-phone"></i> '. $page->tel .'</a></li>' ) .        
                                (!$page->fax ? '' : '<li><a href="tel: '.$page->fax.'"><i class="fas fa-fax"></i> '. $page->fax .'</a></li>' ) .
                                "</ul>", 
                                '<div class="uk-overflow-hidden">'),
		];

	return $m;
}

function renderContact($page) {

	$nb = nb();
	$out = "";

	$out .= "<h3 id='contact-$page->name'>$page->title</h3>";

	if($page->headline)
		$out .= "<h5 class='uk-margin-remove-top'>$page->headline</h5>";

	if($page->address)
		$out .= $nb->htmlWrap(implode("<br>", array_merge(
			explode("\n", $page->address),
			[$page->postcode]
		)), "p");

	$c = [];
	foreach([
		"tel" => "phone",
		"fax" => "fax",
	] as $field => $icon)
		if($page->get($field))
			$c[] = nbTel($page->get($field), [
				"icon" => $icon,
			]);

	if($page->email)
		$c[] = nbMailto($page->email, [
			"icon" => "envelope",
		]);

	if($page->link)
			$c[] = faIcon("link") . " " . $nb->htmlWrap(
				nbUrl($page->link),
				$nb->renderAttr([
					"href" => $page->link,
					"target" => "_blank",
				], "a")
			);

	if(count($c))
		$out .= $nb->htmlWrap(implode("<br>", $c), "p");

	return ukCard($out);
}

/**
 * Output a background image style attribute
 *
 * ~~~~~
 * // Output background image style attributes, limit width to 2048px
 * echo "<div class='hero'" . bgImg($page->banner, [
 *     "width" => 2048,
 * ]) . "></div>";
 * ~~~~~
 *
 * @param Pageimage $image
 * @param array $options
 * @return string
 *
 */
function bgImg($image, array $options = []) {

	// If no image passed, return empty string
	if(!$image->url)
		return "";

	$img = $image;

	// Set default options
	$options = array_merge([
		"width" => 0,
		"height" => 0,
		"left" => $image->focus()["left"],
		"top" => $image->focus()["top"],
		"size" => false,
		"styles" => [],
	], $options);

	// Resize image if specified
	if($options["width"] && $options["height"]) {
		$img = $image->size($options["width"], $options["height"]);
	} else if($options["width"]) {
		$img = $image->width($options["width"]);
	} else if($options["height"]) {
		$img = $image->height($options["height"]);
	}

	// Set styles
	$options["styles"]["background-position"] = "$options[left]% $options[top]%";

	if($options["size"])
		$options["styles"]["background-size"] = $options["size"];

	$styles = [];
	foreach($options["styles"] as $k => $v)
		$styles[] = "$k:$v";

	return nb()->renderAttr([
		"data-uk-img" => true,
		"data-src" => $img->url,
		"style" => implode(";", $styles),
	]);
}

/**
 * Renders a Font Awesome arrow icon
 *
 * The arrow icon is set in the NbWire config so it can be used for both
 * this function and for the UIkit sister function. However as some arrow icon names differ
 * between the two libraries, it may not always be possible to use this effectively.
 *
 * A full list of available icons can be found here: http://fontawesome.io/icons/#directional
 *
 * ~~~~~
 * // Display a large (2x) up arrow
 * echo faArrow("up", "fas", ["2x"]);
 * ~~~~~
 *
 * @param string $dir The arrow direction
 * @param string $prefix The icon prefix (default=`fas`)
 * @param array $classes Any Font Awesome classes that should be added to the icon (e.g. "lg", "fw", "spin")
 * @return string
 *
 */
function faArrow($dir = "right", $prefix = "fas", array $classes = []) {
	return faIcon(nb()->arrow . "-$dir", $prefix, $classes);
}

/**
 * Renders a Font Awesome icon
 *
 * A full list of available icons can be found here: http://fontawesome.io/icons/
 *
 * ~~~~~
 * // Display a large (3x) user icon, fixed width
 * echo faIcon("user", "fas", [
 *     "3x",
 *     "fw",
 * ]);
 * ~~~~~
 *
 * @param string $icon The icon to be displayed
 * @param string $prefix The icon prefix (default=`fas`)
 * @param array $classes Any Font Awesome classes that should be added to the icon (e.g. "lg", "fw", "spin")
 * @return string
 *
 */
function faIcon($icon, $prefix = "fas", array $classes = []) {

	$pre = "fa-";
	$cls = [];
	foreach($classes as $class)
		$cls[] = (strpos($class, $pre) === false ? $pre : "") . $class;

	return "<i class='$prefix fa-$icon" .
		(count($cls) ? " " . implode(" ", $cls) : "") .
	"' aria-hidden='true'></i>";
}

/**
 * Renders a Font Awesome file icon
 *
 * This function renders a standard Font Awesome icon from a given file extension
 *
 * ~~~~~
 * // Display a link to a file with a large icon
 * $file = $page->files->first();
 * echo "<a href='$file->url' download>" . faFileIcon($file->ext, "far", ["lg"]) . " " . $file->description . "</a>";
 * ~~~~~
 *
 * @param string $ext The extension of the file
 * @param string $prefix The icon prefix (default=`fas`)
 * @param array $classes Any Font Awesome classes that should be added to the icon (e.g. "lg", "fw", "spin")
 * @return string
 *
 */
function faFileIcon($ext, $prefix = "far", array $classes = []) {

	switch(strtolower($ext)) {

		case "pdf":
			$icon = "file-pdf";
			break;

		case "doc":
		case "docx":
			$icon = "file-word";
			break;

		case "ppt":
		case "pptx":
			$icon = "file-powerpoint";
			break;

		case "xls":
		case "xlsx":
			$icon = "file-excel";
			break;

		case "aac":
		case "aif":
		case "aiff":
		case "mp3":
		case "wav":
			$icon = "file-audio";
			break;

		case "avi":
		case "flv":
		case "mp4":
		case "mov":
		case "wmv":
			$icon = "video";
			break;

		case "gif":
		case "jpg":
		case "jpeg":
		case "png":
		case "tiff":
			$icon = "file-image";
			break;

		case "zip":
		case "tar":
			$icon = "file-archive";
			break;

		default:
			$icon = "file-alt";
			break;
	}

	return faIcon($icon, $prefix, $classes);
}

/**
 * Renders a static Google Map
 *
 * ~~~~~
 * // Display a static google map
 * echo gmapStatic([
 *     "center" => [60.156444763023934, -1.144344439167412],
 *     "zoom" => 15,
 *     "markers" => [
 *         [
 *             "styles" => "color:blue",
 *             "locations" => "60.156444763023934, -1.144344439167412",
 *         ]
 *     ],
 * ]);
 * ~~~~~
 *
 * @param array $data The data used to construct the map parameters query string
 * - `center` (array|string): Defines the center of the map,
 *  equidistant from all edges of the map
 * - `zoom` (int): Defines the zoom level of the map,
 *  which determines the magnification level of the map. (default=12)
 * - `size` (string): Defines the rectangular dimensions of the map image.
 *  This parameter takes a string of the form {horizontal_value}x{vertical_value}. (default=640x360)
 * - `maptype`(string): Defines the type of map to construct.
 *  There are several possible maptype values, including roadmap, satellite, hybrid, and terrain. (default=roadmap)
 * Please refer to the Maps Static API documentation for all available options: https://developers.google.com/maps/documentation/maps-static/intro
 * @param array $options Options to modify behavior:
 * - `link` (array|bool): Should the map be linked to maps.google.com? (default=false)
 * - `marker` (string): The default marker to be used (default=false)
 * - `key` (string): The API key
 * @return string
 *
 */
function gmapStatic(array $data = [], array $options = []) {

	$nb = nb();

	// The default API key
	$apiKey = $nb->googleApiKey;

	if(!$apiKey)
		return ukAlertDanger("Please add a valid API key to render a static google map.");

	// Set default options
	$options = array_merge([
		"link" => false,
		"marker" => false,
		"key" => $apiKey,
	], $options);

	// Set default data
	$data = array_merge([
		"key" => $options["key"],
		"maptype" => "roadmap",
		"size" => "640x360",
		"zoom" => 12,
	], $data);

	// If `center` is a string, convert to array
	if($nb->isArray("center", $data))
		if(!is_array($data["center"]))
			$data["center"] = explode(",", str_replace(" ", "", $data["center"]));

	// If a `marker` value is set
	if($options["marker"]) {

		// Set the style
		// A link with protocol is inferred as an icon, else a marker color is assumed
		$style = (strpos($options["marker"], "://") === false ? "color:$options[marker]" : "icon:" . urlencode($options["marker"]));

		if($nb->isArray("markers", $data)) {

			// If existing markers don't have a style, add the above
			foreach($data["markers"] as $k => $marker)
				if(!$nb->isArray("styles", $marker))
					$data["markers"][$k]["styles"] = $style;

		} else if($nb->isArray("center", $data)) {

			// If a `center` value is given, create a marker
			$data["markers"] = [
				[
					"styles" => $style,
					"locations" => [
						$data["center"],
					],
				],
			];
		}
	}

	// Convert data to query string
	$q = [];
	foreach($data as $k => $v) {

		if($k == "markers")
			continue;

		if(is_array($v))
			$v = implode(",", $v);

		$q[] = "$k=$v";
	}

	// Convert marker data to query string
	if($nb->isArray("markers", $data)) {

		$markers = [];
		foreach($data["markers"] as $marker) {

			$locations = [];

			if(is_array($marker["locations"])) {

				foreach($marker["locations"] as $location)
					$locations[] = is_array($location) ? implode(",", $location) : $location;

			} else {
				$locations = [str_replace(" ", "", $marker["locations"])];
			}

			$markers[] = "markers=$marker[styles]%7C" . implode("%7C", $locations);
		}

		$q[] = implode("&", $markers);
	}

	return $nb->htmlWrap(
		"<img src='//maps.googleapis.com/maps/api/staticmap?" . implode("&", $q) . "' alt=''>",
		($options["link"] ? "<a href='https://maps.google.com/?q=" .
			implode(",", count($options["link"]) == 2 ? $options["link"] : $data["center"]) .
		"' target='_blank'>" : "")
	);
}

/**
 * Render an item
 *
 * @param Page $page
 * @return string
 *
 */
function renderItem($page) {

	return ukCard(
		nl2br($page->summary),
		[
			$page->title,
			3
		]
	);
}

/**
 * Render items
 *
 * @param PageArray $pages
 * @param string $type
 * @return string
 *
 */
function renderItems($pages, $type = "item") {

	$func = "render" . ucfirst($type);

	$out = "";
	foreach($pages as $page)
		$out .= call_user_func(
			__NAMESPACE__ . "\\$func",
			$page
		);

	return $out;
}

/**
 * Get related pages (children/siblings)
 *
 * @param Page $page
 * @return PageArray
 *
 */
function getRelated($page) {
	return $page->children()->count() ? $page->children() : $page->siblings("id!=$page->id");
}

/**
 * Get related pages (children/siblings)
 *
 * @param Page $page
 * @param array $options The JSON options
 * @return string
 * @see NbWire::renderJSON()
 *
 */
function renderRelated($page, array $options = []) {

	// Set default options
	$options = array_merge([
		"action" => "items",
		"fields" => [
			"image" => "url",
		],
	], $options);

	$pages = getRelated($page);

	return $pages->count() ? nb()->renderJSON($pages, $options) : "";
}

/**
 * Render navigation items
 *
 * @param PageArray $items
 * @param bool $dropdown
 * @return string
 *
 */
function renderNavItems($items = [], $dropdown = true, $dropdownHeader = false) {

	$nb = nb();
	$out = "";

	if(!count($items))
		$items = $nb->navItems;

        $out .= $dropdownHeader? '<li class="dropdown-header">' . strtoupper($dropdownHeader) . '</li>' : '';
        
	foreach($items as $item) {

		$hasChildren = $item->children()->count() && !in_array($item->template->name, [
			"home",
			"posts",
			"studies",
		]);

		$attr = [
			"class" => [],
		];

		if($hasChildren)
			$attr["class"][] = "uk-parent";

		if($item->id == page()->rootParent->id)
			$attr["class"][] = "uk-active";

                $out .= $nb->htmlWrap(

			$nb->htmlWrap(
				$item->title,
				$nb->renderAttr([
                                            "href" => $hasChildren? '#' : $item->url,
                                            "id" => 'submenu-' . $item->id,
                                        ], "a")
                                ) .

			($hasChildren && $dropdown ? $nb->htmlWrap(
				$nb->htmlWrap(
					renderNavItems($item->children(), false, $item->title),
					$nb->renderAttr([
						"class" => [
							"uk-nav",
							"uk-navbar-dropdown-nav",
						]
					], "ul")
				),
				"<div uk-dropdown='offset:0'>"
			) : ""),

			$nb->renderAttr($attr, "li")
		);
	}

	return $out;
}

/**
 * Return a Prev/Next page navigation
 *
 * @param Page $page
 * @return string
 *
 */
function renderPrevNext(Page $page) {

	$nb = nb();

	$lbl = "<span class='uk-label uk-margin-small-bottom'>";
	$ratio = 0.6;
	$class = [
		"uk-width-1-4@l",
		"uk-width-1-3@m",
		"uk-width-1-1",
		"uk-flex",
		"uk-flex-column",
	];

	return $nb->htmlWrap(

		$nb->htmlWrap(

			$nb->htmlWrap(

				$nb->htmlWrap(

					($page->prev->id ?
						$nb->htmlWrap(

							$nb->htmlWrap(
								ukIcon("chevron-left", $ratio) . "Previous",
								$lbl
							) .
							$nb->htmlWrap(
								$page->prev->title,
								"div"
							),

							$nb->renderAttr([
								"href" => $page->prev->url,

							], "a")
						)
					: ""),

					$nb->renderAttr([
						"class" => $class,
					], "div")
				) .

				$nb->htmlWrap(

					$nb->htmlWrap(
						"Back to {$page->parent->title}",
						$nb->renderAttr([
							"href" => $page->parent->url,
							"class" => [
								"uk-button",
								"uk-button-default",
								"uk-visible@m",
							],
						], "a")
					),

					$nb->renderAttr([
						"class" => [
							"uk-width-2-4@l",
							"uk-width-1-3@m",
							"uk-width-1-1",
							"uk-text-center",
							"uk-margin",
						],
					], "div")
				) .

				$nb->htmlWrap(

					($page->next->id ?
						$nb->htmlWrap(

							$nb->htmlWrap(
								"Next" . ukIcon("chevron-right", $ratio),
								$lbl
							) .
							$nb->htmlWrap(
								$page->next->title,
								"div"
							),

							$nb->renderAttr([
								"href" => $page->next->url,

							], "a")
						)
					: ""),

					$nb->renderAttr([
						"class" => array_merge($class, ["uk-text-right@m"]),
					], "div")
				),
				"<div class='uk-flex uk-flex-middle uk-flex-between uk-flex-wrap'>"
			),
			"<div class='uk-container uk-container-$nb->ukContainer'>"
		),
		"<div class='uk-margin-medium'>"
	);
}

/**
 * Returns an XML sitemap
 *
 * @param array $paths
 * @return string
 *
 */
function renderSitemapXML(array $paths = []) {

	$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
	'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	array_unshift($paths, "/"); // prepend homepage

	foreach($paths as $path) {

		$page = pages()->get($path);

		if(!$page->id)
			continue;

		$out .= renderSitemapPage($page);

		if($page->numChildren)
			$out .= renderSitemapChildren($page);
	}

	$out .= "\n</urlset>";

	return $out;
}

/**
 * Returns a single <url> node for XML sitemap
 *
 * @param Page $page
 * @return string
 *
 */
function renderSitemapPage(Page $page) {

	return "\n<url>" .
		"\n\t<loc>{$page->httpUrl}</loc>" .
		"\n\t<lastmod>" . date("Y-m-d", $page->modified) . "</lastmod>" .
	"\n</url>";
}

/**
 * Returns all children of a given Page as <url> nodes for XML sitemap
 *
 * @param Page $page
 * @return string
 *
 */
function renderSitemapChildren(Page $page) {

	$out = "";
	$pages = pages();
	$newParents = $pages->newPageArray();
	$children = $page->children;

	foreach($children as $child) {

		$out .= renderSitemapPage($child);

		if($child->numChildren) $newParents->add($child);
			else $pages->uncache($child);
	}

	foreach($newParents as $newParent) {

		$out .= renderSitemapChildren($newParent);
		$pages->uncache($newParent);
	}

	return $out;
}

/**
 * Render one featured page
 * @param Page $page
 */
function renderFeatured($page) {
    
    $nb = nb();
    
    $title =  
        $nb->htmlWrap(
            $nb->htmlWrap(    
                $nb->htmlWrap(
                    $page->title,
                    '<h2 class="uk-card-title">'
                ) . $nb->htmlWrap(
                    'Find out more<i class="fas fa-long-arrow-alt-right"></i>',
                    '<span class="uk-button uk-button-link">'
                ),
                'uk-card-body uk-background-primary'),
            'uk-overlay uk-position-cover uk-padding-xsmall'
        );
    
    $link = '<a href="' . $page->url . '" class="read-more" aria-label="Read More"></a>';
    
    // find an image
    $resizedImg = getThumb($page);
    
    //var_dump($resizedImg); exit;
    $img = $resizedImg ? $nb->htmlWrap(
                '<img src="'. $resizedImg->url .'" data-src="'. $resizedImg->url .'" alt="' . $page->title . ' image" data-uk-img="" data-uk-cover="">
                 <canvas height="200" width="280"></canvas>',
                 'uk-cover-container'
            ) : '';
    
    $out = 
        $nb->htmlWrap(    
            $nb->htmlWrap(
                $img . $title . $link,
                'uk-card card-featured has-bottom-gradient'
            ), 'div'
        );
        
    return $out;
}

/**
 * Get the thumb image of a page from the banner or page images
 * @param Page $page
 * @return Image
 */
function getThumb($page, $width = 600, $height = null) {
    
    // thumbnail?
    if ($page->thumb) {
        return $page->thumb->size($width, $height);
    }
	// banner?
    if ($page->banner) {
        return $page->banner->size($width, $height);
    }
    // images?
    if (count($page->images)) {
        return $page->images->first()->size($width, $height);
    } 
    // gallery ins repeater?
    if(count($page->blocks)) {
        foreach ($page->blocks as $block) {
            if (in_array($block->type, ['gallery', 'slideshow'])) {
                return $block->gallery->first()->size($width, $height);
            }
        }
    }
    $nb = nb();
    return $nb->getImage($page, [
        'width' =>$width,
        'height' =>$height,
        ]);
    
    
}

/**
 * Render Service "card"
 * Initially developed for the homepage
 * @param Page $service
 * @param string $class 
 * @param bool $other if service passed as first argument is an extra service (not a page)
 * @return string
 */
function renderServiceCard(Page $service, $bgClass = 'uk-background-default', $other = false) {
    $nb = nb();
    
    if (!$other)
        $resizedImage = getThumb($service);
    else 
        $resizedImage = $service->thumb;
    
    $img = $resizedImage ? $nb->htmlWrap(
                '<img src="' . $resizedImage->url . '" data-src="' . $resizedImage->url . '" alt="'. $service->title .' image" data-uk-img="" data-uk-cover="">',
                '<div class="thumbnail">'
            ) : '';
    
    $link = (!$other ? '<a href="' . $service->url . '" class="read-more" aria-label="Read More"></a>' : '');
    $summary = (!$other ? nbSummary($service, ["summary", "intro", "meta_desc"], 120) : $service->intro);
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                $img . $nb->htmlWrap(
                    '<h3 class="entry-title">'. $service->title .'</h3>
                     <div class="entry-summary">'. $summary .'</div>' .
                     (!$other ? 
                            '<span class="uk-button uk-button-small uk-button-link"><span class="label">MORE</span><i class="fas fa-long-arrow-alt-right"></i></span>'
                            : '<div style="line-height:1"> &nbsp; </div>'),
                    '<div class="entry-body '. $bgClass .' uk-padding-small uk-width-1-1">'
                ) . $link, 
                '<div class="entry-item">'
            ), 'div'
        );
    
    return $out;
}

/**
 * Render Study "card"
 * Initially developed for the homepage slider
 * @param Page $study
 */
function renderStudyCard(Page $study, $width = 600, $height = null, $itemClasses = null) {
    $nb = nb();
    $resizedImage = getThumb($study, $width, $height);
    $img = $resizedImage ? $nb->htmlWrap(
                '<img src="' . $resizedImage->url . '" data-src="' . $resizedImage->url . '" alt="'. $study->title .' image" data-uk-img="" data-uk-cover="">',
                '<div class="thumbnail">'
            ) : '';
    $relatedService = $study->services->count ? $study->services->first() : false;
    $header = $nb->htmlWrap(
            '<div class="entry-meta underlined uk-width-1-1">'. ($relatedService ? $relatedService->title : '') .'</div>
             <h2 class="entry-title uk-text-truncate">'. $study->title .'</h2>',
            '<div class="entry-header uk-light uk-height-small entry-gradient">'
            );
    $body = $nb->htmlWrap(
            '<p class="entry-summary uk-padding-small">'. nbSummary($study) .'</p>' . 
            $nb->htmlWrap(
                    '<div class="map-location"><i class="fas fa-map-marker-alt"></i>'. $study->location .'</div>
                     <span class="uk-button uk-button-link uk-light">FIND OUT MORE<i class="fas fa-long-arrow-alt-right"></i></span>',
                    '<div class="entry-bar uk-padding-small">'
                ),
            '<div class="entry-body uk-position-bottom">'
        );
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
            $img . $nb->htmlWrap(
                $header . $nb->htmlWrap(
                    $body,
                    'uk-position-cover-spaced'
                ),
                'uk-position-cover'
            ) . '<a href="'. $study->url .'" class="read-more" aria-label=""></a>'
            ,
            '<div class="entry-item large-item ' . ($itemClasses ? implode(',', $itemClasses) : '') . '">'
        ),
        'div');
    return $out;
}

function renderNewsCard(Page $page) {
    $nb = nb();
    
    $resizedImage = getThumb($page, 400);
    $img = $resizedImage ? '<img src = "'. $resizedImage->url .'" alt = "'. $page->title .' image" data-uk-cover="" />' : '';
    $top = $nb->htmlWrap(
            '<div class = "meta">'. $nb->formatDate($page->date_pub, 'd M Y') .'</div>'.
            $img .
            '<canvas height = "220" width = "480"></canvas>',
            '<div class="uk-card-media-top uk-cover-container">'
            );
    
    $body = $nb->htmlWrap(
            '<h3 class = "uk-card-title">'. $page->title .'</h3>
            <p>'. nbSummary($page) .'</p>' .
            $nb->htmlWrap(
                    'CONTINUE READING<i class="fas fa-long-arrow-alt-right"></i>',
                    '<span class="uk-button uk-button-link">'
                ),
            'uk-card-body uk-background-default'
            );
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                $top . $body
                . '<a href = "'. $page->url .'" class = "uk-position-cover"></a>',
                'uk-card news-card'
            ), 'div');
    
    return $out;
}

/**
 * Render a link 
 * Initially developed for homepage
 * @param $link
 * @return string html 
 */
function renderHomeLink($link) {
    
    $nb = nb();
//    var_dump($link->thumb); exit;
    $img = $nb->htmlWrap(
                '<img src="'. $link->thumb->url .'" data-src="'. $link->thumb->url .'" alt="'. $link->title .' image" data-uk-img="" data-uk-cover="">',
                '<div class="thumbnail">'
            );
    
    $body = $nb->htmlWrap(
                $nb->htmlWrap(
                    '<h3 class="entry-title">'. $link->title .'</h3>
                     <div class="entry-summary">'. $link->summary .'</div>',
                    '<div class="entry-body uk-position-bottom uk-background-primary">'
                ) . 
                $nb->htmlWrap(
                    '<span class="uk-button uk-button-small uk-button-link"><span>VISIT OUR SITE</span><i class="fas fa-external-link-alt"></i></span>',
                    'uk-position-bottom-right'
                ),
                'uk-position-cover-spaced'
            );
    
    $a = '<a href="'. $link->link .'" class="read-more" aria-label="Read More" rel="noopener noreferrer" target="_blank"></a>';
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                $img . $body . $a,
                '<div class="entry-item large-item v2 home-links-boxes">'
            ), 'div');

    return $out;
}

function renderBreadCrumbs(Page $page) {
    $nb = nb();
    $parents = $page->parents;

    $links = '';
    foreach($parents as $parent) {
        $url = $parent->url;
        $links .= $nb->htmlWrap(
                "<a href='$url'>{$parent->title}</a>",
                "li"
            );
    }
    $links .= "<li><span>{$page->title}</span></li>";

    return $nb->htmlWrap(
                $links,
                '<ul class="uk-breadcrumb">'
            );   
}

/**
 * Render the 'header' of a case study
 * @param type $rows
 * @param Page $page
 */
function renderStudyHighlights($rows, Page $page, Pages $pages) {  
    $nb = nb();
    $out = '';
    
    $icons = [
        'Client' => 'fa-user',
        'Location' => 'fa-map-marker-alt',
        'Start Date' => 'fa-calendar-alt',
        'Duration' => 'fa-clock',
        'Value' => 'fa-pound-sign',
        'Services Provided' => 'fa-forward',
    ];
    
    $i = 0;
        
    foreach ($rows as $row) {
        $type = $row[0];
        $value = $row[1];
        
        $html = $nb->htmlWrap(
                '<i class="fas '.$icons[$type].' primary-color"></i>
                <h5>'.$type.'</h5>
                <div class="label">'.$value.'</div>',
                '<div class="contact-box">'
        );
        
        if (!($i%2)) {
            $line = $html;
        } else {
            $line .= $html;
            $line = $nb->htmlWrap(
                    $line,
                    'div'
            );
            $out .= $line;
        }        
        ++$i;
    } 
    
    $out .= '<div class="uk-width-expand uk-visible@l"></div>';
    
    // find contact with related branch
    $out .= renderStudyContact($page, $pages);
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                $nb->htmlWrap(
                    $out 
                    ,'<div class=" uk-child-width-1-3@s uk-child-width-1-4@l uk-grid-medium" data-uk-grid="">')
                ,'<div class="bar uk-background-grey uk-tile">')
            ,'uk-container uk-container-medium');
    
    return $out;
}

/**
 * render the contact box in case study page
 * @param Page $page
 */
function renderStudyContact(Page $page, Pages $pages, $linkedPage = 1467) {
    $nb = nb();
    // find the branch
    if ($page->branch->first()) {
        $branch = $pages->get($page->branch->first->id);
    } else { // default contact 
        $branch = $pages->get(1120);
    }
    
    $locate = $pages->get($linkedPage);
    
    // find the first division from the branch
    $division = $pages->findOne('template=division, branch=' . $branch->id . ', include=all');
    
    $out = $nb->htmlWrap(
                $nb->htmlWrap(
                   '<h4 class="widget-title">Let\'s talk about your project:</h4>
                    <p>Lerwick office:</p>
                    <ul class="uk-list">
                        <li><i class="fas fa-phone" data-fa-transform="rotate-90"></i>'. nbTel($division->tel).'</li>
                    </ul>
                    <a href="'. $locate->url. '" class="uk-button uk-button-secondary ghost pull-icon-right">Locate Nearest Office<i class="fas fa-long-arrow-alt-right"></i></a>'
                , '<div class="widget widget-talk">'
                )
            , 'div'//'uk-position-center-right'
            );
    return $out;
}

function renderStudyIntro($intro, $title = 'Project Background') {
    return renderIntro($intro, $title);
}

function renderIntro($intro, $title = false) {
    $nb = nb();
    return $nb->htmlWrap(
        $nb->htmlWrap(
            ($title ? '<h1>' . $title . '</h1>' : '' ) . '
            <p class="uk-text-lead">' . $intro . '</p>',
            '<div class="content uk-margin-large-bottom">'
        ), 'uk-container uk-container-small'
    );
}

function renderShareButtons($module) {
    $nb = nb();
    return $nb->htmlWrap(
           '<small class="">SHARE A STORY ON:</small>' . 
            $module->render()
           ,'<div class="share-story centered uk-margin-medium-top ">'
        );
}

function renderFloatingShareButtons($module) {
    $nb = nb();
    
    return $nb->htmlWrap(
                $module->render(),
                '<div class="floating-tool">'
            );
}


/**
 * render the previous and next pages block (Study)
 * @param \ProcessWire\Page $page currentPage
 */
function renderPrevAndNextBlock(Page $page, $title = 'CASE STUDIES') {
    
    $nb = nb();
    
    $previous = $nb->htmlWrap(
           $page->prev()->title ? ('<a href="'.$page->prev()->url.'" class="prev">
                <small>Previous</small>
                <i class="fas fa-long-arrow-alt-left"></i>
                '.$page->prev()->title.'
            </a>') : '', 
        'uk-width-1-3');
    
    $allPages = $nb->htmlWrap(
            '<a href="'.$page->parent()->url.'" class="uk-button uk-button-large ghost uk-button-secondary">VIEW ALL ' 
            . strtoupper($title) .'</a>'
        ,'uk-text-center uk-width-1-3');
    
    $next = $nb->htmlWrap(
           $page->next()->title ? ('<a href="'.$page->next()->url.'" class="prev">
                <small>Next</small>
                <i class="fas fa-long-arrow-alt-right"></i>
                '.$page->next()->title.'
            </a>') : '', 
        'uk-text-right uk-width-1-3');
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                $nb->htmlWrap(
                    $previous . $allPages . $next
                    ,'<div class="uk-grid-small uk-flex uk-flex-middle uk-flex-between" data-uk-grid="">')
                ,'uk-container')
            ,'<div class="single-blog-navigation">');
    
    return $out;
}

/**
 * render related studies
 * initially developed for Study page
 * PageArray $related related studies to display
 */
function renderRelatedStudies($related) {
    $nb = nb();
    
    // render each card
    foreach ($related as $study) {
        $studyCards .= renderStudyCard($study, 300, 500, ['entry-item-portrait']);
    }
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                    $nb->htmlWrap(
                            '<h2 class="section-title uk-text-center">Related Cases</h2>'
                            , 'section-header')
                    . $nb->htmlWrap(
                            $nb->htmlWrap(
                                    $studyCards
                                    , '<div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-4@l" data-uk-grid="">')
                            , 'uk-padding-xsmall')
                    ,'uk-container')
            , 'uk-section uk-background-grey z0');
    
    return $out;
}


function renderRelatedServices($related) {
    $nb = nb();
    $serviceCards = '';
    // render each card
    foreach ($related as $service) {
        $serviceCards .= renderServiceCard($service);
    }
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                $nb->htmlWrap(
                    $nb->htmlWrap(
                            '<h2 class="section-title uk-text-center">Related Services</h2>'
                            , 'section-header')
                    . $nb->htmlWrap(
                            $serviceCards
                            , '<div class="service-grid uk-grid-small uk-child-width-1-3@s uk-child-width-1-4@l" data-uk-grid="">')
                    ,'<div class="section-header">')
                , 'uk-container'),
            'uk-section uk-background-grey z0');
    
    return $out;
}

function renderSubPages($subpages) {
    $nb = nb();
    $cards = '';
    // render each card
    foreach ($subpages as $page) {
        $cards .= renderServiceCard($page, 'bg-light-grey');
    }
    
    return $nb->htmlWrap(
            $cards,
            '<div class="service-grid uk-grid-small uk-child-width-1-1@s uk-child-width-1-2@l" data-uk-grid="">'
    );
}

/**
 * render location card
 * initially developed for locations template
 * @param Page $location
 * @return string
 */
function renderLocationCard(Page $location) {
    $nb = nb();
    
    $thumb = getThumb($location);
   
    $list = '';
    if ($location->services->count) {
        foreach ($location->services as $service) {
            $list .= '<li>' . $service->title . '</li>';
        }
        $list = $nb->htmlWrap(
                $nb->htmlWrap(
                        $nb->htmlWrap(
                            $list
                            , 'ul')
                        ,'<div class="content uk-light">')
                ,'<div class="entry-summary">');       
    }
    
    $out = $nb->htmlWrap(
            $nb->htmlWrap(
                    $nb->htmlWrap(
                            '<img data-src="'. $thumb->url .'" alt="" data-uk-img="" data-uk-cover="">'
                            ,'<div class="thumbnail">') .
                    $nb->htmlWrap(
                            $nb->htmlWrap(
                                    '<h3 class="entry-title"><i class="fas fa-map-marker-alt"></i>'.$location->title.'</h3>' .
                                    $list
                                    ,'<div class="entry-body uk-position-bottom uk-background-primary">') 
                            . $nb->htmlWrap(
                                    '<span class="uk-button uk-button-small uk-button-link">'
                                    . ' <span>VISIT OUR SITE</span>'
                                    . ' <i class="fas fa-long-arrow-alt-right"></i>'
                                    . '</span>'
                                    ,'uk-position-bottom-right')
                            ,'uk-position-cover-spaced') . 
                    '<a href="' . $location->url . '" class="read-more" aria-label="Read More"></a>'
                    ,'<div class="entry-item large-item v2 with-large-title">')
            ,'div');
    
    return $out;
}


function renderPersonnelCard($personnel, $body = false) {
    $nb = nb();
    return $nb->htmlWrap(
                $nb->htmlWrap(
                        $nb->htmlWrap(
                                '<h3 class="uk-card-title">'. $personnel->title .'</h3>
                                <small class="position">'. $personnel->headline .'</small>'
                                . ($body ? $personnel->body : '')
                                . ($personnel->email ? '<i class="fas fa-envelope"></i> ' . nbMailto($personnel->email) : ''),
                                'uk-card-body')
                        ,'<div class="uk-card personal-card uk-card-small">')
                ,'div');
}

/**
 * Render available plant for hire
 * Should be used for page Services => Plant Tool Hires and Sales only
 * @param Page $availablePlant
 * @param Page $locationsPage
 */
function renderAvailablePlant($availablePlant, Page $locationsPage) {
    if ($availablePlant->count) {
        $nb = nb();
        $out = '<h3>Plant available for hire:</h3>';
        $categoriesList = '';
        foreach ($availablePlant as $category) {
            $categoriesList .= $nb->htmlWrap(
                        '<a href= "#cat-' . $category->id . '" uk-scroll>' . $category->title . '</a>',
                        'li'
                    );
        }
        $out .= $nb->htmlWrap(
                $categoriesList,
                $nb->renderAttr(
                        [
                            'class' => 'multi-col-list',
                        ], 
                        'ul'
                        )
                );
        
        $locations = $locationsPage->children();
        $detailsList = '<th></th>'
                . ' <th>Plant Type</th>';
        foreach ($locations as $location) {
            $detailsList .= '<th>' . $location->title . '</th>';
        }
        $detailsList = $nb->htmlWrap(
                    $detailsList,
                    'tr'
                );
        
        foreach ($availablePlant as $category) {
            $detailsList .= renderPlantHireLine($category, $locations, true);
            if ($category->available_plant_details->count) {
                foreach ($category->available_plant_details as $model) {
                    $detailsList .= renderPlantHireLine($model, $locations);
                }
            }
        }
        
        $out .= $nb->htmlWrap(
                $nb->htmlWrap(
                        $detailsList,
                        $nb->renderAttr([
                            'class' => [
                                    'uk-table',
                                    'plants',
                                ], 
                            ], 'table')
                        ),
                'uk-overflow-auto');
        
        return $out;
    }
    return '';
}


function renderPlantHireLine($item, $locations, $title = false) {
    $nb = nb();
    
    $line = '<td>' . ($item->thumb ? '<img src="' . $item->thumb->size(57, 40)->url . '" alt="Image title">' : '') . '</td>'
    . '<td>' . $item->title . '</td>';
    
    foreach ($locations as $location) {
        $line .= '<td>' . ($item->location_select->has($location) ? '<i class="fas fa-check"></i>' : '') . '</td>';
    }
    $out .= $nb->htmlWrap(
            $line, $nb->renderAttr([
                'class' => [ !$title ?: 'group'],
                'id' => 'cat-' . $item->id,
                    ], 'tr')
    );
    
    return $out;
}

function getEquipmentCategories($categories) {
    $out = nb()->htmlWrap(
            $categories->each('<div><button data-filter="{name}" class="uk-button category-filter uk-text-center">'
                    . '<img src="{icon.url}"><br>'
                    . '<span>{title}</span>'
                    . '</button></div>'),
            '<div class="uk-margin uk-text-center category-filters uk-grid uk-grid-small uk-child-width-expand@s" data-uk-grid>');
    return $out;
}
function getEquipmentLocations($locations) {
    $out = nb()->htmlWrap(
            $locations->each('<div><button data-filter="{name}" class="uk-button uk-button-large location-filter uk-text-center uk-width-1-1">{title}</button></div>'),
            '<div class="uk-margin uk-text-center location-filters uk-grid uk-grid-small uk-child-width-expand@s" data-uk-grid>');
    return $out;
}