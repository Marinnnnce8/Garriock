var activeInfoWindow;


var $site = {

    breakpoint: 640,
    screen_width: verge.viewportW(),

    init: function () {
        this.blocks();
        this.navigation.init();
        //this.maps.init();
        this.gallery();
    },


    gallery: function () {

        $(".gallery-grid").each(function(){

            var $that = $(this);
            
            $that.justifiedGallery({
                margins: 7,
                rowHeight: $that.data("height") ? $that.data("height") : 200,
                maxRowHeight: "15%",
                lastRow: "justify"
            });
        });
    },

    blocks: function() {

		if(!$("[data-nb-block]").length)
			return;

		$("[data-nb-block]").each(function() {

			var $block = $(this),
				type = $block.data("nb-block");

			switch(type) {

				case "content":

					// Table
					$block.find("table")
						.addClass("uk-table")
						.addClass("uk-table-justify")
						.addClass("uk-table-divider")
						.wrap("<div class='uk-overflow-auto'></div>");

					// Inline Images Lightbox
					$.each($block.find("a").filter(function() {

						return $(this).attr("href") ? $(this).attr("href").match(/\.(jpg|jpeg|png)/i) : null;

					}), function() {

						var $a = $(this),
							$figure = $a.parent();

						$figure.attr("data-uk-lightbox", "animation: fade");
						$a.addClass("nb-zoom-in").attr("data-caption", $figure.find("figcaption").html());
					});

					break;

				case "embed":

					$block.find("iframe")
						.attr("uk-responsive", true);

					UIkit.update();

					break;
			}
		})
	},

    navigation: {

        init: function () {
            // Progressivly collapsing navigation
            $('.okayNav').okayNav({
                parent: '',
                align_right: true,
                swipe_enabled: false,
                itemHidden: function () {
                }, // Will trigger after an item moves to the hidden navigation
                itemDisplayed: function () {
                } // Will trigger after an item moves to the visible navigation
            });

            var conf = {
                // toggle: that.data("toggler"),
                boundary: (($site.screen_width >= 640 ) ? ".navbar-main > li" : "#site-header"),
                boundaryAlign: ($site.screen_width >= 640 ) ? false : true,
                pos: ($site.screen_width >= 640 ) ? "bottom-left" : "bottom-justify",
                offset: 0,
                duration: 350,
                delayHide: 0,
                ukAnimation: "uk-animation-slide-top-small,uk-animation-fade"
            };


            // Top level drops
            $("div.drop").each(function () {
                var that = $(this);
                conf["toggle"] = that.data("toggler");
                UIkit.drop(this, conf);
            });

        },
    },

    maps: {

    },

    preload: function () {
    }, 
    
    /**
	 * Handles Google Map API data encoded as JSON in the data-nb-map attribute
	 * Should be called from the callback function e.g. "..&callback=initMap", with initMap calling this
	 *
	 */
	gmap: function() {

		if(!$("[data-nb-gmap]").length)
			return;

		$data = {}; // Holds the data

		$("[data-nb-gmap]").each(function() {

			var $element = $(this);

			// If no id is present on the element, give it one.
			if($element.attr("id") == undefined)
				$element.attr("id", "map-" + (Math.floor((Math.random() * 100) + 1)));

			var mapID = $element.attr("id");

			// Setup up our $site.gmaps var
			$site.gmaps[mapID] = {
				"map": {}, // The map
				"layers": {}, // KML layers
				"markers": {}, // Markers
				"windows": {} // InfoWindows
			};

			// Get the map data
			$data[mapID] = $element.data("nb-gmap");

			// Create map
			$site.gmaps[mapID]["map"] = new google.maps.Map(document.getElementById(mapID), $data[mapID]["options"]);

			// Cycle through layer groups
			for(var groupID in $data[mapID]["layers"]) {

				// Setup group var
				$site.gmaps[mapID]["layers"][groupID] = {};

				// Cycle through the layers in each group
				for(var layerID in $data[mapID]["layers"][groupID]) {

					// Create a layer
					$site.gmaps[mapID]["layers"][groupID][layerID] = new google.maps.KmlLayer($data[mapID]["layers"][groupID][layerID]);

					// Add the layer
					$site.gmaps[mapID]["layers"][groupID][layerID].setMap($site.gmaps[mapID]["map"]);
				}
			}

			// Cycle through marker groups
			var bounds = new google.maps.LatLngBounds();
                        
			for(var groupID in $data[mapID]["markers"]) {

				// Setup group vars
				$site.gmaps[mapID]["markers"][groupID] = {};
				$site.gmaps[mapID]["windows"][groupID] = {};

				for(var markerID in $data[mapID]["markers"][groupID]) {

					var $marker = $data[mapID]["markers"][groupID][markerID];

					if($.type($marker.icon) !== "string") {

						$marker.icon = {
							url: $marker.icon.url,
							size: new google.maps.Size(
								$marker["icon"]["size"][0],
								$marker["icon"]["size"][1]
							),
							origin: new google.maps.Point(
								$marker["icon"]["origin"][0],
								$marker["icon"]["origin"][1]
							),
							anchor: new google.maps.Point(
								$marker["icon"]["anchor"][0],
								$marker["icon"]["anchor"][1]
							),
						}
					}

					// Assign this marker to this map
					$marker["map"] = $site.gmaps[mapID]["map"];

					// Create the marker
					$site.gmaps[mapID]["markers"][groupID][markerID] = new google.maps.Marker($marker);

					// Add to bounds
					bounds.extend($site.gmaps[mapID]["markers"][groupID][markerID].getPosition());

					// If the marker has an InfoWindow, create it
					if($marker["window"])
						$site.gmaps[mapID]["windows"][groupID][markerID] = new google.maps.InfoWindow($data[mapID]["markers"][groupID][markerID]["window"]);
				}
			}

			if($element[0].hasAttribute("data-nb-gmap-autozoom"))
				$site.gmaps[mapID]["map"].fitBounds(bounds);
		});

		// Set the listener for opening InfoWindows
		$.each($site.gmaps, function(mapID) {

			$.each($site.gmaps[mapID]["windows"], function(groupID) {

				$.each($site.gmaps[mapID]["windows"][groupID], function(markerID) {

					$site.gmaps[mapID]["markers"][groupID][markerID].addListener("click", function() {
                                                $site.gmaps[mapID]
                                                
						if (activeInfoWindow) activeInfoWindow.close();
                                                $site.gmaps[mapID]["windows"][groupID][markerID].open(
							$site.gmaps[mapID]["map"],
							$site.gmaps[mapID]["markers"][groupID][markerID]
						);
                                                activeInfoWindow = $site.gmaps[mapID]["windows"][groupID][markerID];
					});
				});
			});
		});

		// Handle toggles for layers and markers
		$("[data-nb-gmap-toggle]").each(function() {

			var $btn = $(this),
				$btnData = $btn.data("nb-gmap-toggle"),
				mapID = $btnData.map,
				typeID = $btnData.type,
				groupID = $btnData.group;

			// If the $btn doesn't have a toggle class, add it
			if(!$btn.hasClass("nb-toggle-on") && !$btn.hasClass("nb-toggle-off"))
				$btn.addClass("nb-toggle-on");

			$btn.click(function() {

				// Toggle Class
				$btn.toggleClass("nb-toggle-on nb-toggle-off");

				if($btn.hasClass("nb-toggle-on")) {

					// If on, set markers
					$.each($site.gmaps[mapID][typeID][groupID], function(id) {
						$site.gmaps[mapID][typeID][groupID][id].setMap($site.gmaps[mapID]["map"]);
					});

				} else {

					// If off, remove markers
					$.each($site.gmaps[mapID][typeID][groupID], function(id) {
						$site.gmaps[mapID][typeID][groupID][id].setMap(null);
					});
				}
			})
		});
	},
    gmaps: {} // Holds the map objects
}


$("document").ready(function () {

    $site.init();

    $(window).on("load", function () {
        $("html").addClass("content-loaded");
        setTimeout(function () {
            $("#preloader-wrapper").remove();
        }, 500);
    });
});


/**
 * Debounce function
 *
 * @param func
 * @param wait
 * @param immediate
 * @returns {Function}
 *
 * @usage
 *
 * // Create the listener function
 * var updateLayout = _.debounce(function(e) {
 *	        // Does all the layout updating here
 *   }, 500); // Maximum run of once per 500 milliseconds
 *
 *   // Add the event listener
 *   window.addEventListener("resize", updateLayout, false);
 */
function doo_debounce(func, wait, immediate) {

    "use strict";

    var timeout;
    return function () {
        var context = this,
            args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    document.getElementById("totop").style.display = "block";
  } else {
    document.getElementById("totop").style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
} 