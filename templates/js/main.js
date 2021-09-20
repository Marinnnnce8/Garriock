/**
 * main.js
 *
 * Site scripts
 *
 */

/**
 * Extensions of $nb
 *
 */
$.extend($nb.json.render, {
	
	item: function($item) {
            var $services = $item.services;
            if ($services.length > 0) {
                var $service = $services[0].title;
            } else {
                var $service = '';
            }
            
            return $nb.htmlWrap(
                $nb.htmlWrap(
                    ($item.image ? 
				$nb.htmlWrap(
                                    $nb.renderAttr({
                                            "src": "#",
                                            "alt": $item.title,
                                            "data-uk-img": true,
                                            "data-src": $item.image,
                                    }, "img"),
                                    $nb.renderAttr({
                                            "class": 'thumbmail'
                                    }, "div")
                                ) : '') +
                                $nb.htmlWrap(
                                    $nb.htmlWrap(
                                        $nb.htmlWrap(
                                            '<div class="entry-meta underlined">' + $service + '</div>' +
                                            '<h2 class="entry-title">' + $item.title + '</h2>'
                                            , $nb.renderAttr({
                                                'class': 'entry-header uk-light'
                                            },'div')
                                        ) + 
                                        $nb.htmlWrap(
                                            '<p class="entry-summary uk-padding-small">' + ($item.summary ? $item.summary : '') + '</p>' +
                                            '<div class="entry-bar uk-padding-small">' +
                                                '<span class="uk-button uk-button-link">FIND OUT MORE<i class="fas fa-long-arrow-alt-right"></i></span>'+
                                            '</div>'
                                            , $nb.renderAttr({
                                                'class': 'entry-body uk-position-bottom'
                                            }, 'div')
                                        ), $nb.renderAttr({
                                            'class': 'uk-position-cover-spaced'
                                        }, 'div')
                                    ), $nb.renderAttr({
                                        'class': 'uk-position-cover entry-top-gradient-bg'
                                        }, 'div') 
                                ) +'<a href="' + $item.url + '" class="read-more" aria-label=""></a>'
                                ,'<div class="entry-item white-item large-item">'
                            ), 'div');
	},

	items: function($items, $config) {
            
		var out = "",
			width = $config.width == undefined ? "uk-child-width-1-3@s" : $config.width,
			itemType = $config.type;

		if(!$.isFunction($nb.json.render[itemType]))
			itemType = "item";

		if($config.slider) {

			for(var id in $items)
				out += "<li>" +
					this[itemType]($items[id]) +
				"</li>";

			return "<div class='uk-slider uk-slider-darker-dotnav uk-slider-container' data-uk-slider>" +
				"<div class='uk-position-relative'>" +
					"<ul class='uk-slider-items uk-grid-small uk-child-width-1-3@s' data-uk-grid>" +
						out +
					"</ul>" +
					($config.slidenav == false ? "" : "<div class='navigation-wrapper uk-visible@s'>" +
						"<a class='uk-slidenav-large uk-hidden-hover uk-light' href='#' data-uk-slider-item='previous'>" +
							"<i class='icon-arrow-line-left'></i>" +
						"</a>" +
						"<a class='uk-slidenav-large uk-hidden-hover uk-light' href='#' data-uk-slider-item='next'>" +
							"<i class='icon-arrow-line-right'></i>" +
						"</a>" +
					"</div>") +
				"</div>" +
				($config.dotnav == false ? "" : "<ul class='uk-slider-nav uk-dotnav uk-dotnav-squared uk-flex-center'></ul>") +
			"</div>";

		} else {

			for(var id in $items)
				out += this[itemType]($items[id]);

			return $nb.htmlWrap(
				out,
				$nb.renderAttr({
					"class": [
						"uk-grid-match",
						width,
						"uk-flex",
						"uk-flex-center",
					],
					"data-uk-grid": true
				}, "div")
			);
		}
	},

	posts: function($items, $config) {
            $config.width = "uk-grid-40 uk-child-width-1-2@m";
            return this.items($items, $config);
	},
        
        equipment_sales: function($items, $config) {
            var out = '';
            for(var id in $items)
                   out += this.equipment_sales_item($items[id], $config);
            
            return $nb.htmlWrap(out, 'div');
            
        },
        
        equipment_sales_item($item, $config) {
            
            var slideshow = $nb.htmlWrap('slideshow comes here', 'div');
            var commentSection = $nb.htmlWrap($item.intro, 'p');
            return $nb.htmlWrap(
                $nb.htmlWrap(
                        slideshow + 
                        commentSection,
                        '<div>'
                    ) +
                $nb.htmlWrap(
					'<div class="uk-flex uk-flex-middle uk-flex-between"><div class="uk-flex"><img src="' + $item.category.icon + '">' + '<span class="uk-text-uppercase uk-text-bold uk-flex uk-flex-bottom">'+
					$item.category.title + '</span></div>' + '<span class="uk-text-uppercase uk-text-bold">'+
					$item.equipment_location  + '</span></div>' +'<br>'+
                    $item.category.title +'<br>'+
                    $item.headline +'<br>'+
                    $item.duration + '<br>' +
                    $item.year +'<br>'+
                    $item.equipment_id +'<br>'+
                    $item.equipment_price +'<br>'+
                    $item.equipment_condition +'<br>'+
                    $item.contact.email + ' ' + $item.contact.title + '<br>'+
                    $item.title +'<br>',
                    '<div>'
                    ),
            '<div class="uk-margin-large uk-width-1-1 uk-card uk-grid-collapse uk-child-width-1-2@s" data-uk-grid>');
        },
        
	search: function($items, $config) {
		return this.items($items, $config);
	},

	service: function($item) {
                return $nb.htmlWrap(
                    $nb.htmlWrap(
                        ($item.image ? $nb.htmlWrap($nb.renderAttr({
                                    "src": "#",
                                    "alt": $item.title,
                                    "data-uk-img": true,
                                    "data-uk-cover": true,
                                    "data-src": $item.image
                                }, "img"),
                                $nb.renderAttr({
                                    "class":"thumbnail"
                                }, "div")) : ""
                        ) + 
                        $nb.htmlWrap(
                            "<h3 class='entry-title'>"+$item.title+"</h3>"+
                            "<div class='entry-summary'>"+$item.title+"</div>"+
                            "<span class='uk-button uk-button-small uk-button-link'><span class='label'>MORE</span><i class='fas fa-long-arrow-alt-right'></i></span>",
                            $nb.renderAttr({
                                'class': 'entry-body uk-background-grey uk-padding-small uk-width-1-1'
                            }, 'div')
                        ) + 
                        $nb.renderAttr({
                            'href': $item.url,
                            'class': 'read-more',
                            'aria-label': 'Read More'
                        }, "a")
                        ,
                        "<div class='entry-item'>"), 
                    'div');
	},

	services: function($items, $config) {
		$config.width = "service-grid uk-grid-small uk-child-width-1-3@s uk-child-width-1-4@l";
		$config.type = "service";
		return this.items($items, $config);
	},

	study: function($item) {
            var $services = $item.services;
            if ($services.length > 0) {
                var $service = $services[0].title;
            } else {
                var $service = '';
            }
            
            return $nb.htmlWrap(
                $nb.htmlWrap(
                    ($item.image ? 
				$nb.htmlWrap(
                                    $nb.renderAttr({
                                            "src": "#",
                                            "alt": $item.title,
                                            "data-uk-img": true,
                                            "data-src": $item.image,
                                    }, "img"),
                                    $nb.renderAttr({
                                            "class": 'thumbmail'
                                    }, "div")
                                ) : '') +
                                $nb.htmlWrap(
                                    
                                        $nb.htmlWrap(
                                            '<div class="entry-meta underlined uk-width-1-1">' + $service + '</div>' +
                                            '<h2 class="entry-title uk-text-break">' + $item.title + '</h2>'
                                            , $nb.renderAttr({
                                                'class': 'entry-header uk-light uk-height-small entry-gradient'
                                            },'div')
                                        ) + $nb.htmlWrap(
                                        $nb.htmlWrap(
                                            '<p class="entry-summary uk-padding-small">' + ($item.summary ? $item.summary : '') + '</p>' +
                                            '<div class="entry-bar uk-padding-small">' +
                                                '<div class="map-location"><i class="fas fa-map-marker-alt"></i>'+ $item.location +'</div>' +
                                                '<span class="uk-button uk-button-link uk-light">FIND OUT MORE<i class="fas fa-long-arrow-alt-right"></i></span>'+
                                            '</div>'
                                            , $nb.renderAttr({
                                                'class': 'entry-body uk-position-bottom'
                                            }, 'div')
                                        ), $nb.renderAttr({
                                            'class': 'uk-position-cover-spaced'
                                        }, 'div')
                                    ), $nb.renderAttr({
                                        'class': 'uk-position-cover entry-top-gradient-bg'
                                        }, 'div') 
                                ) +'<a href="' + $item.url + '" class="read-more" aria-label=""></a>'
                                ,'<div class="entry-item large-item">'
                            ), 'div');
	},

	studies: function($items, $config) {
            $config.width = "uk-grid-40 uk-child-width-1-2@m";
            $config.type = "study";
            return this.items($items, $config);
    },

	upload: function($item, $config) {

		$config = $.extend({
			"lightbox": false,
		}, $config);

		var image = "",
			col = "1-1",
			$attr = {
				"class": "uk-grid-small",
				"data-uk-grid": true,
			},
			$data = JSON.stringify($item.data),
			description = $item.description ? $item.description : "";

		if($item.src) {

			var $attrImg = {
					src: "#",
					alt: description,
					"data-src": $item.src,
					"data-uk-img": true,
				},
				$attrWrap = {
					class: "uk-width-1-4"
				},
				elem = "div";

			if($item.src.includes("data:image")) {

				$attrImg.src = $item.src;
				$attrImg["data-src"] = false;
				$attrImg["data-uk-image"] = false;
			}

			if($config.lightbox) {
				$attr["data-uk-lightbox"] = true;
				$attrWrap.href = $item.src;
				elem = "a";
			}

			col = "3-4";
			image += $nb.htmlWrap(
				$nb.renderAttr($attrImg, "img"),
				$nb.renderAttr($attrWrap, elem)
			);
		}

		return $nb.htmlWrap(

			$nb.htmlWrap(

				image +

				$nb.htmlWrap(

					"<h4 class='uk-margin-remove uk-text-break'>" + $item.name + "</h4>" +
					"<div class='uk-text-meta'>" + $item.size + "</div>" +
					($config.description ? $nb.renderAttr({
						"type": "text",
						"placeholder": "Caption/Description...",
						"name": "",
						"value": description,
						"class": [
							"uk-input",
							"uk-margin-top",
						],
						"data-nb-upload-description": description,
					}, "input") : "") +
					$nb.htmlWrap(

						"<button" + $nb.renderAttr({
							"type": "button",
							"class": [
								"uk-button",
								"uk-button-danger",
								"uk-button-small",
							],
						}) + " data-" + $item.remove + ">" +
							$nb.ukIcon("trash", 0.75) +
							" Remove" +
						"</button>",

						"<div class='uk-margin-top'>"
					),

					"<div class='uk-width-" + col + "'>"
				),

				$nb.renderAttr($attr, "div")

			) +
			($data.id ? "" : "<input type='hidden' name='" + $item.input + "[" + $item.name + "]' value='" + $item.name + "'>"),

			$nb.renderAttr({
				"class": [
					"uk-placeholder",
					"uk-padding-small",
					"uk-margin-remove-top"
				],
				"data-nb-upload-item": $data
			}, "div")
		);
	}
});
