<?php namespace ProcessWire;

/**
 * UIkit Functions
 *
 */

/**
 * Renders a UIkit Accordion
 *
 * Full documentation on the Accordion component can be found here: https://getuikit.com/docs/accordion
 *
 * ~~~~~
 * // Display an accordion with the 4th item open and a slower animation
 * echo ukAccordion($page->items, [
 *     "active" => 3,
 *     "duration" => 512,
 * ]);
 * ~~~~~
 *
 * @param array|PageArray|RepeaterPageArray $items The items to display in the accordion
 * @param array $options Options to modify behavior. The values set by default are:
 * - `active` (int): The index of the open item (default=0)
 * - `duration` (int): The open/close animation duration in milliseconds (default=256)
 *
 * Please refer to the uk-accordion documentation for all available options: https://getuikit.com/docs/accordion#component-options
 * @return string
 *
 */
function ukAccordion($items, array $options = []) {

	// Set default options
	$options = array_merge([
		"active" => 0,
		"duration" => 256,
	], $options);

	// Convert to array
	if($items instanceof PageArray || $items instanceof RepeaterPageArray) {

		$a = [];
		foreach($items as $item)
			$a[$item->title] = $item->body;

		$items = $a;
	}

	$out = "";
	foreach($items as $title => $body)
		$out .= nb()->htmlWrap(
			"<a class='uk-accordion-title' href='#'>$title</a>" .
			"<div class='uk-accordion-content'>$body</div>",
			"<li>"
		);

	return nb()->htmlWrap(
		$out,
		nb()->renderAttr([
			"data-uk-accordion" => $options
		], "ul")
	);
}

/**
 * Renders a UIkit Alert
 *
 * Full documentation on the Alert component can be found here: https://getuikit.com/docs/alert
 *
 * ~~~~~
 * // Display a "danger" message with no close button
 * echo ukAlert("I'm sorry Dave, I'm afraid I can't do that", "danger", [
 *     "close" => false,
 * ]);
 * ~~~~~
 *
 * @param string $html Text/html to display in the alert box
 * @param string $type The UIkit style: `primary | success | warning | danger`
 * @param array $options Options to modify behavior. The values set by default are:
 * - `animation` (bool|string): Fade out or use the Animation component (default=true)
 * - `close` (bool): Should a close button be displayed? (default=true)
 * - `duration` (int): Animation duration in milliseconds (default=256)
 *
 * Please refer to the uk-alert documentation for all available options: https://getuikit.com/docs/alert#component-options
 * @return string
 *
 */
function ukAlert($html = "", $type = "success", array $options = []) {

	// Set default options
	$options = array_merge([
		"animation" => true,
		"close" => true,
		"duration" => 256,
	], $options);

	// close is not a uk-alert option
	$close = $options["close"];
	unset($options["close"]);

	return nb()->htmlWrap(
		($close && $html ? "<a class='uk-alert-close' data-uk-close></a>" : "") .
		$html,
		nb()->renderAttr([
			"class" => [
				"uk-alert-$type",
			],
			"data-uk-alert" => $options,
		], "div")
	);
}

/**
 * Render a success alert, shortcut for ukAlert("message", "success");
 *
 * @param string $html
 * @param array $options
 * @return string
 *
 */
function ukAlertSuccess($html = "", array $options = []) {
	return ukAlert($html, "success", $options);
}

/**
 * Render a primary alert, shortcut for ukAlert("message", "primary");
 *
 * @param string $html
 * @param array $options
 * @return string
 *
 */
function ukAlertPrimary($html = "", array $options = []) {
	return ukAlert($html, "primary", $options);
}

/**
 * Render a warning alert, shortcut for ukAlert("message", "warning");
 *
 * @param string $html
 * @param array $options
 * @return string
 *
 */
function ukAlertWarning($html = "", array $options = []) {
	return ukAlert($html, "warning", $options);
}

/**
 * Render a danger alert, shortcut for ukAlert("message", "danger");
 *
 * @param string $html
 * @param array $options
 * @return string
 *
 */
function ukAlertDanger($html = "", array $options = []) {
	return ukAlert($html, "danger", $options);
}

/**
 * Renders a UIkit arrow icon
 *
 * The arrow icon is set in the NbWire config so it can be used for both
 * this module and for NBFontAwesome. However as some arrow icon names differ
 * between the two libraries, it may not always be possible to use this effectively.
 *
 * A full list of available direction icons can be found here: https://getuikit.com/docs/icon#library
 *
 * ~~~~~
 * // Display a large (2x) up arrow
 * echo ukArrow("up", 2);
 * ~~~~~
 *
 * @param string $dir The arrow direction
 * @param int $ratio The size of the icon
 * @return string
 *
 */
function ukArrow($dir = "right", $ratio = 1) {
	return ukIcon(nb()->arrow . "-$dir", $ratio);
}

/**
 * Render a UIkit breadcrumb list from the given page
 *
 * @param Page|PageArray $page
 * @param array $options Additional options to modify default behavior:
 *  - `attr` (array): Additional attributes to apply to the <ul.uk-breadcrumb>.
 *  - `appendCurrent` (bool): Append current page as non-linked item at the end? (default=true).
 * @return string
 *
 */
function ukBreadcrumb($page = null, array $options = []) {

	if(is_null($page))
		$page = page();

	if($page instanceof Page) {

		$items = $page->breadCrumbs ?: $page->parents();

	} else {

		$items = $page;
		$page = $items->last();
		$items->remove($page);
	}

	$nb = nb();

	$options = array_merge([
		"attr" => [],
		"appendCurrent" => true,
	], $options);

	$options["attr"] = array_merge([
		"class" => [],
	], $options["attr"]);

	$options["attr"]["class"] = array_merge($options["attr"]["class"], [
		"uk-breadcrumb",
	]);

	return $nb->htmlWrap(
		$items->each("<li><a href='{url}'>{title}</a></li>") .
		($options["appendCurrent"] ? "<li><span>$page->title</span></li>" : ""),
		$nb->renderAttr($options["attr"], "ul")
	);
}

/**
 * Renders a UIkit card
 *
 * ~~~~~
 * // An example
 * echo ukCard("<p>Test</p>");
 * ~~~~~
 *
 * @param string $body
 * @param string|array $header
 * @param string $footer
 * @param array $classes
 * @return string
 *
 */
function ukCard($body = "", $header = "", $footer = "", array $classes = []) {

	$nb = nb();

	// Set default classes
	if(!count($classes))
		$classes = [
			"uk-card-default",
			"uk-margin-bottom",
		];

	$out = "";

	if(is_array($header))
		$header = call_user_func_array(__NAMESPACE__ . "\ukCardTitle", $header);

	if($header)
		$out .= $nb->htmlWrap(
			$header,
			"<div class='uk-card-header'>"
		);

	if($body)
		$out .= $nb->htmlWrap(
			$body,
			"<div class='uk-card-body'>"
		);

	if($footer)
		$out .= $nb->htmlWrap(
			$footer,
			"<div class='uk-card-footer'>"
		);

	return $out ? $nb->htmlWrap(
		$out,
		$nb->renderAttr([
			"class" => array_merge(["uk-card"], $classes),
		], "div")
	) : "";
}

/**
 * Renders a UIkit card title
 *
 * ~~~~~
 * // An example
 * echo ukCardTitle("Title");
 * ~~~~~
 *
 * @param string $title The title
 * @param int $h The heading value
 * @return string
 *
 */
function ukCardTitle($title, $h = 2) {
	return "<h$h class='uk-card-title' id='" . nb()->cleanID($title) . "'>$title</h$h>";
}

/**
 * Renders a UIkit icon
 *
 * A full list of available icons can be found here: https://getuikit.com/docs/icon#library
 *
 * ~~~~~
 * // Display a large (3x) user icon
 * echo ukIcon("user", 3);
 * ~~~~~
 *
 * @param string $icon The icon to be displayed
 * @param int $ratio The size of the icon
 * @return string
 *
 */
function ukIcon($icon, $ratio = 1) {

	return "<span data-uk-icon='" . json_encode([
		"icon" => $icon,
		"ratio" => $ratio,
	]) . "'></span>";
}

/**
 * Renders a UIkit Nav
 *
 * ~~~~~
 * // Render the section navigation for the page
 * echo ukNav($page);
 * ~~~~~
 *
 * @param Page|PageArray $nav
 * @param array $options Options to modify behavior.
 *  - `attr` (array): An array of attributes rendered on the main <ul> element.
 *  - `exclude` (array): An array of template names that should be excluded from the navigation.
 *  - `prependParent` (bool): When rendering children, should the parent be prepended?
 * @return string
 *
 */
function ukNav($nav, array $options = []) {

	$nb = nb();

	// Set default options
	$options = array_merge([
		"attr" => [],
		"attrSub" => [
			"class" => [
				"uk-nav-sub",
			],
		],
		"attrSubItems" => [
			"class" => [
				"uk-nav-sub-items",
			],
		],
		"exclude" => [],
		"prependParent" => false,
	], $options);

	// Set default attributes
	$attr = array_merge([
		"class" => [
			"uk-nav",
			"uk-nav-default",
			"uk-nav-parent-icon",
		],
		"data-uk-nav" => true,
	], $options["attr"]);
	$options["attr"] = $options["attrSub"];

	if($nav instanceof Page) {

		$page = $nav;
		$items = $page->id == 1 ? pages()->find("id=1") : $page->rootParent->children();

	} else if($nav instanceof PageArray && $nav->count()) {

		$items = $nav;
		$page = page();

	} else {

		return "";
	}

	// Return blank if a nav cannot or should not be rendered
	if(!$items->count() || in_array($page->template->name, $options["exclude"]))
		return "";

	$out = "";
	foreach($items as $item)
		$out .= ukNavItem($item, $page, $options);

	return $nb->htmlWrap($out, $nb->renderAttr($attr, "ul"));
}

/**
 * Renders a UIkit Nav item
 *
 * ~~~~~
 * // Render a navigation item for
 * $out .= ukNavItem($item, $page, $attr);
 * ~~~~~
 *
 * @param Page $item The child item being rendered
 * @param Page $page The page the nav is being rendered on
 * @param array $options Options to modify behavior
 * @param bool $children Should the child pages be rendered?
 * @return string
 * @see ukNav()
 *
 */
function ukNavItem($item, $page, array $options = [], $children = true) {

	$nb = nb();

	$isActive = $item->id == $page->id || ($page->parents->has($item) && $item->id !== 1);

	$attr = array_merge([
		"class" => [],
	], $options["attr"]);

	if($isActive)
		$attr["class"][] = "uk-active";

	$out = "<a href='{$item->url}'>{$item->title}</a>";
	if($item->children()->count() && !in_array($item->template->name, $options["exclude"]) && $children) {

		$attr["class"][] = "uk-parent";

		if($isActive)
			$attr["class"][] = "uk-open";

		$subOptions = $options;
		$subOptions["attr"] = $options["attrSubItems"];

		$o = $options["prependParent"] ? ukNavItem($item, $page, $subOptions, false) : "";

		foreach($item->children() as $child)
			$o .= ukNavItem($child, $page, $subOptions);

		$out .= $nb->htmlWrap($o, $nb->renderAttr($attr, "ul"));
	}

	return $nb->htmlWrap(
		$out,
		$nb->renderAttr($attr, "li")
	);
}

/**
 * Renders a UIkit Slider arrow button
 *
 * For use in Slideshow and Slider components
 *
 * ~~~~~
 * // Display a 'previous' arrow button
 * echo ukSlidenav("user", ["uk-position-center-left"]);
 * ~~~~~
 *
 * @param string $dir The direction (previous/next)
 * @param array $classes An array of classes for the element
 * @return string
 *
 */
function ukSlidenav($dir = "previous", array $classes = []) {

	return nb()->renderAttr([
		"class" => $classes,
		"href" => "#",
		"data-uk-slideshow-item" => $dir,
		"data-uk-slidenav-$dir" => true,
	], "a", true);
}

/**
 * Renders a UIkit Slider
 *
 * Full documentation on the Slider component can be found here: https://getuikit.com/docs/slider
 *
 * ~~~~~
 * // Display a slider, resize images to 512px in width
 * echo ukSlider($page->images, [
 *     "width" => 512,
 * ]);
 * ~~~~~
 *
 * @param Pageimages $images The images to display
 * @param array $options Options to modify behavior:
 * - `class` (array): Classes for the slider wrapper
 * - `height` (int): The height of the thumbnail image (default=`$nb->width` * 0.75)
 * - `lightbox` (array): UIkit Lightbox options
 * Please refer to the uk-lightbox documentation for all available options: https://getuikit.com/docs/lightbox#component-options
 * - `nav` (bool): Show a dot nav below the slider (default=false)
 * - `slider` (array): UIkit Slider options
 * Please refer to the uk-slider documentation for all available options: https://getuikit.com/docs/slider#component-options
 * - `width` (int): The width of the thumbnail image (default=`$nb->width`)
 *
 * @return string
 *
 */
function ukSlider(Pageimages $images, array $options = []) {

	$nb = nb();
	$width = $nb->width ?: ($nb->height ?: 768);

	// Set default options
	$options = array_merge([
		"class" => [
			"uk-position-relative",
			"uk-visible-toggle",
			"uk-light",
		],
		"height" => $width * 0.75,
		"id" => "uk-slider-" . $nb->randomNumber(),
		"lightbox" => [],
		"nav" => true,
		"slider" => [],
		"sliderClass" => [
			"uk-grid",
			"uk-grid-small",
			"uk-child-width-1-2",
			"uk-child-width-1-3@s",
		],
		"width" => $width,
	], $options);

	// Set default slider options
	$slider = array_merge([
		"sets" => true,
	], $options["slider"]);

	// Set default lightbox options
	$lightbox = array_merge([
		"animation" => "fade",
	], $options["lightbox"]);

	$items = "";
	foreach($images as $image)
		$items .= $nb->htmlWrap(
			$nb->htmlWrap(
				$nb->renderAttr([
					"src" => $nb->pixel,
					"alt" => $image->description,
					"data-src" => $image->size($options["width"], $options["height"])->url,
					"data-uk-img" => [
						"target" => "#$options[id]",
					],
				], "img"),
				$nb->renderAttr([
					"href" => $image->url,
					"data-caption" => $image->description,
				], "a")
			),
			"<li>"
		);

	$navClasses = [
		"uk-position-small",
		"uk-hidden-hover"
	];

	return $nb->htmlWrap(

		$nb->htmlWrap(

			$nb->htmlWrap(

				$nb->htmlWrap(
					$items,
					$nb->renderAttr([
						"class" => array_merge(["uk-slider-items"], $options["sliderClass"]),
						"data-uk-lightbox" => $lightbox
					], "ul")
				),
				"<div class='uk-slider-container'>"
			) .

			ukSlidenav("previous", array_merge([
				"uk-position-center-left",
			], $navClasses)) .

			ukSlidenav("next", array_merge([
				"uk-position-center-right",
			], $navClasses)),

			$nb->renderAttr([
				"class" => $options["class"],
			], "div")
		) .
		($options["nav"] ? "<ul class='uk-slider-nav uk-dotnav uk-flex-center uk-margin'></ul>" : ""),

		$nb->renderAttr([
			"data-uk-slider" => (count($slider) ? $slider : true),
			"id" => $options["id"],
		], "div")
	);
}

/**
 * Renders a UIkit Slideshow
 *
 * Full documentation on the Slideshow component can be found here: https://getuikit.com/docs/slideshow
 *
 * ~~~~~
 * // Display a slideshow, resize images to 512px in width
 * echo ukSlideshow($page->images, [
 *     "width" => 512,
 * ]);
 * ~~~~~
 *
 * @param Pageimages $images The images to display
 * @param array $options Options to modify behavior:
 * - `caption` (array): Options to modify caption behaviour
 * - `class` (array): Classes for the slideshow wrapper
 * - `lightbox` (array|bool): UIkit Lightbox options
 * Please refer to the uk-lightbox documentation for all available options: https://getuikit.com/docs/lightbox#component-options
 * - `nav` (bool): Show a dot nav below the slideshow (default=false)
 * - `slideshow` (array): UIkit Slideshow options
 * Please refer to the uk-slideshow documentation for all available options: https://getuikit.com/docs/slideshow#component-options
 * - `width` (int): The width of the thumbnail image (default=`$nb->width`)
 *
 * @return string
 *
 */
function ukSlideshow(Pageimages $images, array $options = []) {

	$nb = nb();

	// Set default options
	$options = array_merge([
		"caption" => [],
		"class" => [
			"uk-position-relative",
			"uk-visible-toggle",
			"uk-light",
		],
		"id" => "uk-slideshow-" . $nb->randomNumber(),
		"lightbox" => [],
		"nav" => false,
		"slideshow" => [],
		"width" => ($nb->width ?: ($nb->height ?: 768)),
	], $options);

	// Set default slideshow options
	$slideshow = array_merge([
		"animation" => "fade",
		"autoplay" => true,
		"autoplay-interval" => 4096,
		"ratio" => "16:9",
	], $options["slideshow"]);

	// Set image height from slideshow ratio
	$ratio = $slideshow["ratio"] ? explode(":", $slideshow["ratio"]) : 0;

	// Set default lightbox options
	$lightbox = is_array($options["lightbox"]) ? array_merge([
		"animation" => "fade",
	], $options["lightbox"]) : $options["lightbox"];

	// Set default caption options
	$caption = array_merge([
		"show" => true,
		"class" => [
			"uk-overlay",
			"uk-overlay-primary",
			"uk-position-bottom",
			"uk-padding-small",
			"uk-text-center",
			"uk-transition-slide-bottom",
		],
	], $options["caption"]);

	$items = "";
	foreach($images as $image)
		$items .= $nb->htmlWrap(

			$nb->htmlWrap(

				$nb->renderAttr([
					"src" => $nb->pixel,
					"alt" => $image->description,
					"data-src" => $image->size(
						$options["width"],
						($ratio ? ($ratio[1] / $ratio[0]) * $options["width"] : $nb->height)
					)->url,
					"data-uk-img" => [
						"target" => "#$options[id]",
					],
				], "img"),

				($lightbox ? $nb->renderAttr([
					"href" => $image->url,
					"class" => [
						"uk-position-center",
						"nb-zoom-in",
					],
					"data-caption" => $image->description,
				], "a") : "")
			) .

			($image->description && $caption["show"] ? $nb->htmlWrap(
				"<p class='uk-margin-remove'>$image->description</p>",
				$nb->renderAttr([
					"class" => $caption["class"]
				], "div")
			) : ""),
			"<li>"
		);

	$navClasses = [
		"uk-position-small",
		"uk-hidden-hover"
	];

	return $nb->htmlWrap(

		$nb->htmlWrap(

			$nb->htmlWrap(
				$items,
				$nb->renderAttr([
					"class" => array_merge(["uk-slideshow-items"], $options["class"]),
					"data-uk-lightbox" => $lightbox,
				], "ul")
			) .

			ukSlidenav("previous", array_merge([
				"uk-position-center-left",
			], $navClasses)) .

			ukSlidenav("next", array_merge([
				"uk-position-center-right",
			], $navClasses)),

			$nb->renderAttr([
				"class" => $options["class"],
			], "div")
		) .

		($options["nav"] ? "<ul class='uk-slideshow-nav uk-dotnav uk-flex-center uk-margin'></ul>" : ""),

		$nb->renderAttr([
			"data-uk-slideshow" => $slideshow,
			"id" => $options["id"],
		], "div")
	);
}

/**
 * Renders UIkit Tabs
 *
 * Full documentation on the Tab component can be found here: https://getuikit.com/docs/tab
 *
 * ~~~~~
 * // Display tabs with the 5th item active and a quick animation
 * echo ukTabs($page->items, [
 *     "active" => 5,
 *     "duration" => 128
 * ]);
 * ~~~~~
 *
 * @param array|PageArray|RepeaterPageArray $items The items to display in tabs
 * @param array $options Options to modify behavior. The values set by default are:
 * - `active` (int): The index of the open item (default=0)
 * - `animation` (string): The type of animation used (default="uk-animation-fade")
 * - `duration` (int): The open/close animation duration in milliseconds (default=256)
 *
 * Please refer to the uk-tab documentation for all available options: https://getuikit.com/docs/tab#component-options
 * @return string
 *
 */
function ukTabs($items, array $options = []) {

	$nb = nb();

	// Set default options
	$options = array_merge([
		"active" => 0,
		"animation" => "uk-animation-fade",
		"duration" => 256,
	], $options);

	// Convert to array
	if($items instanceof PageArray || $items instanceof RepeaterPageArray) {

		$a = [];
		foreach($items as $item)
			$a[$item->title] = $item->body;

		$items = $a;
	}

	$tabs = "";
	$contents = "";
	foreach($items as $title => $body) {

		$tabs .= "<li><a href='#'>$title</a></li>";
		$contents .= "<li>$body</li>";
	}

	return $nb->htmlWrap(
		$tabs,
		$nb->renderAttr([
			"data-uk-tab" => $options
		], "ul")
	) .
	$nb->htmlWrap(
		$contents,
		$nb->renderAttr([
			"class" => "uk-switcher"
		], "ul")
	);
}
