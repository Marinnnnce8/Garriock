/**
 * NB Site Scripts
 *
 * @version 3.0.1
 * @author Chris Thomson
 * @author NB Communication
 *
 */

if(typeof jQuery === "undefined")
	throw new Error("$nb requires jQuery. jQuery must be included before this script.");

if(typeof UIkit === "undefined")
	throw new Error("$nb requires UIkit. UIkit must be included before this script.");

(function($) {

	$nb = {

		/**
		 * Defaults
		 *
		 */
		defaults: {
			arrow: "chevron",
			offset: 128,
			speed: 256,
			textLoad: "Load More",
			textLoading: "Loading",
			ukAlert: {
				options: {
					animation: true,
					close: true,
					duration: 256
				},
				style: "primary"
			},
			ukNotification: {
				status: "primary",
				pos: "top-right",
				timeout: 4096,
			}
		},

		/**
		 * Initialize
		 *
		 */
		init: function() {

			this.mailto();
			this.tel();
			this.json.init();
			this.form.init();
			this.upload.init();
		},

		/**
		 * Convert a base64 encoded JSON string to an object
		 *
		 * @param string str The string to convert
		 * @return object
		 *
		 */
		base64toJSON: function(str) {
			return JSON.parse(atob(str))
		},

		/**
		 * Debounce
		 *
		 * Wrap taxing tasks with this...
		 *
		 * * ~~~~~
		 * // Debounce a window resize function
		 * // Log a timestamp on every fire
		 * $(window).on("resize", $nb.debounce(function() {
		 *     console.log(Date.now());
		 * }, 256));
		 * ~~~~~
		 *
		 * @param function func The function to limit
		 * @param int wait The time to wait between fires
		 * @param bool immediate trigger the function on the leading edge, instead of the trailing.
		 * @return function
		 *
		 */
		debounce: function(func, wait, immediate) {

			var timeout;

			return function() {

				var context = this,
					args = arguments,
					later = function() {

						timeout = null;

						if(!immediate)
							func.apply(context, args);
					},
					callNow = immediate && !timeout;

				clearTimeout(timeout);
				timeout = setTimeout(later, wait);

				if(callNow)
					func.apply(context, args);
			}
		},

		/**
		 * Returns a Font Awesome arrow icon
		 *
		 * @param string dir The arrow direction
 		 * @param string $prefix The icon prefix
		 * @param array classes Classes to modify the icon (e.g. "lg", "fw", "spin")
		 * @param string arrow The Font Awesome arrow icon to use
		 * @return string
		 *
		 */
		faArrow: function(dir, prefix, classes, arrow) {

			return this.faIcon(
				(arrow ? arrow : this.defaults.arrow) + "-" + (dir ? dir : "right"),
				prefix,
				classes
			);
		},

		/**
		 * Returns a Font Awesome icon
		 *
		 * @param string icon The Font Awesome icon to return
 		 * @param string $prefix The icon prefix (default=`fas`)
		 * @param array classes Classes to modify the icon (e.g. "lg", "fw", "spin")
		 * @return string
		 *
		 */
		faIcon: function(icon, prefix, classes) {

			var array = [];
			if(classes !== undefined)
				for(var i = 0; i < classes.length; i++)
					array[i] = "fa-" + classes[i];

			return "<i class='" +
				(prefix ? prefix : "fas") +
				" fa-" + icon.replace("fa-", "") +
				(array.length ? " " + array.join(" ") : "") +
			"' aria-hidden='true'></i>";
		},

		/**
		 * Form
		 *
		 */
		form: {

			/**
			 * Variables
			 *
			 */
			btnText: "Send",
			button: {},
			object: {},
			request: {},

			/**
			 * Initialize
			 *
			 */
			init: function() {

				if(!$("[data-nb-form]").length)
					return;

				$(document).on("submit", "[data-nb-form]", function(e) {

					e.preventDefault ? e.preventDefault() : e.returnValue = false;

					// Set variables
					$nb.form.object = $(this);
					$nb.form.button = $nb.form.object.find("button[type=submit]");

					var $honey = $nb.form.object.find("input.uk-hidden[autocomplete=off]"),
						confirm = $nb.form.object.data("nb-form").confirm,
						valid = true;

					// If honeypot has been populated, return error message
					if($honey.length && $honey.val()) {

						$nb.form.msg("danger");
						valid = false;
					}

					// Make sure required fields are populated
					$.each($nb.form.object.find(":input[required]:visible"), function() {

						if(!$(this).val())
							valid = false;
					});

					if(valid) {

						// If a confirmation is required
						if(confirm) {

							UIkit.modal.confirm(confirm).then(function() {

								// Confirmed, post the data
								$nb.form.post();

							}, function() {

								// Not confirmed, reset and return
								$nb.form.reset();
								return false;
							});

						} else {

							// No confirmation required, post the data
							$nb.form.post();
						}

					} else {

						UIkit.modal.alert("Please complete all required form fields.");

						// Reset and return
						$nb.form.reset();
						return false;
					}
				});
			},

			/**
			 * POST fail callback
			 *
			 * @param object jqXHR
			 * @param string textStatus
			 * @param string errorThrown
			 *
			 */
			onFail: function(jqXHR, textStatus, errorThrown) {

				if(this.button)
					this.button.html(this.btnText);

				this.reset();
			},

			/**
			 * POST success callback
			 *
			 * @param object $result The result of the request
			 *
			 */
			onSuccess: function($result) {

				switch($result.response) {

					case 200: // Successful

						this.button.remove();
						this.msg("success");

						$.each(this.object.find(":input"), function() {
							$(this).attr("disabled", true);
						});

						$(".g-recaptcha").remove();

						break;

					case 401: // Unauthorised

						UIkit.modal.alert($result.message);
						if(this.button)
							this.button.html(this.btnText);

						break;

					default:

						this.msg("danger");

						break;
				}

				this.reset();
			},

			/**
			 * Display form message
			 *
			 * @param string style The UIkit style
			 * @param bool hide Hide fields?
			 *
			 */
			msg: function(style, hide) {

				var $form = this.object,
					$msg = $form.data("nb-form").msg;

				$form.prepend(
					$nb.ukAlert(
						($msg ? ($msg[style] ? $msg[style] : $nb.defaults[style]) : $nb.defaults[style]),
						style,
						{
							close: false
						}
					)
				);

				// Scroll to top of form
				$("html, body").animate({
					scrollTop: $form.offset().top - $nb.defaults.offset
				}, $nb.defaults.speed * 2);

				// Remove fields on error
				hide = hide === undefined ? (style == "danger" ? true : false) : hide;
				if(hide)
					$form.find("fieldset").remove();
			},

			/**
			 * Post the data
			 *
			 */
			post: function() {

				var $form = this.object,
					$btn = this.button,
					$options = $form.data("nb-form");

				// Set the button to loading
				this.btnText = $btn.html();
				$btn.html(($options.loading ? $options.loading : $nb.defaults.loading) + " " + $nb.ukSpinner(0.4));

				// POST the data
				this.request = $.post(
					$form.attr("action"),
					$form.serializeArray(),
					function($result) {
						$nb.form.onSuccess($result);
					},
					"json"
				)
				.fail(function(jqXHR, textStatus, errorThrown) {
					$nb.form.onFail(jqXHR, textStatus, errorThrown);
				});
			},

			/**
			 * Reset form variables
			 *
			 */
			reset: function() {

				this.btnText = "Send";
				this.button = {};
				this.object = {};
				this.request = {};
			}
		},

		/**
		 * Formatting functions
		 *
		 */
		format: {

			/**
			 * Applies formatting to data-nb-format elements
			 *
			 */
			auto: function() {

				if(!$("[data-nb-format]").length)
					return;

				$("[data-nb-format]").each(function() {

					var $item = $(this),
						method = $item.data("nb-format");

					if($.isFunction($nb.format[method]))
						$item.html($nb.format[method]($item.html()));
				})
			},

			/**
			 * UK Postcode
			 *
			 * @param string str The string to format
			 * @return string
			 *
			 */
			postcode: function(str) {

				if(str.length) {

					// The postcode should be uppercase
					str = str.toUpperCase();

					// The postcode should only have one space
					// Remove all spaces and non-alphanumeric characters first
					str = str.replace(/[^a-z0-9]/gi, "");

					// The length of the outward code
					var ol = str.length - 3;

					// The postcode should have a space
					// between outward and inward codes
					a = str.split(" ");
					if(a.length == 1)
						str = a[0].slice(0, ol) + " " + a[0].slice(ol);

					// 2nd half should be a zero
					a = str.split(" ");
					if(a[1].slice(0, 1) == "O")
						str = a[0].slice(0, ol) + " 0" + a[1].slice(1);
				}

				return str;
			},

			/**
			 * Return a numerical value as a price
			 *
			 * @param int|float value The value to format
			 * @param string locale The BCP 47 locale to use (default=en-GB)
			 * @param string currency The ISO 4217 currency code to use
			 * @return string
			 *
			 */
			money: function(value, locale, currency) {

				locale = locale == undefined ? "en-GB" : locale;
				currency = currency == undefined ? "GBP" : currency;

				var $options = {
					style: "currency",
					currency: currency
				};

				if((typeof Intl == "object" && Intl && typeof Intl.NumberFormat == "function")) {

					value = new Intl.NumberFormat(locale, $options).format(value);

				} else {

					value = value.toLocaleString(locale, $options);
				}

				var n = value.split("."),
					whole = n[0],
					decimal = n[1];

				if(decimal.length == 1)
					decimal += "0";
				
				return decimal == "00" ? whole : [whole, decimal].join(".");
			},

			/**
			 * Sentence case
			 *
			 * @param string str The string to format
			 * @return string
			 *
			 */
			sentenceCase: function(str) {

				var a = str.split(". "),
					b = [];

				for(var i = 0; i < a.length; i++) {
					var c = a[i];
					b.push(c.charAt(0).toUpperCase() + c.slice(1))
				}
				return b.join(". ");
			},

			/**
			 * Return url with or without the protocol
			 *
			 * @param string url The URL to be processed
			 * @param bool protocol Should the protocol be displayed
			 * @return string
			 *
			 */
			url: function(url, protocol) {

				if(url.length) {

					if(url.substr(0, 4) !== "http") {
						url = "http://" + url;
					} else if(!/^https?:\/\//i.test(url)) {
						url = "http://" + url;
					}
				}

				if(!protocol)
					url = url.replace("http://", "").replace("https://", "").replace(/\/+$/g, "");

				return url;
			},

			/**
			 * Returns a valid username from input
			 *
			 * @param string str The string to format
			 * @return string
			 *
			 */
			username: function(str) {

				str = $.trim(str);
				str = str.toLowerCase();
				str = str.replace(/['"\u0022\u0027\u00AB\u00BB\u2018\u2019\u201A\u201B\u201C\u201D\u201E\u201F\u2039\u203A\u300C\u300D\u300E\u300F\u301D\u301E\u301F\uFE41\uFE42\uFE43\uFE44\uFF02\uFF07\uFF62\uFF63]/g, "");
				str = str.replace(/[^-_.a-z0-9 ]/g, "-");
				str = str.replace(/\s+/g, "-");
				str = str.replace(/--+/g, "-");
				str = str.replace(/\.\.+/g, ".");
				str = str.replace(/(\.-|-\.)/g, "-");
				str = str.replace(/(^[-_.]+|[-_.]+$)/g, "");

				if(str.length > 128)
					str = $.trim(str).substring(0, 128).split("-").slice(0, -1).join(" ");

				return str;
			}
		},

		/**
		 * Wraps a string in a given html string
		 *
		 * @param string str The html string to be wrapped
		 * @param string wrap The html wrapper
		 * @return string
		 *
		 */
		htmlWrap: function(str, wrap) {

			if(wrap == undefined || wrap == "")
				return str;

			wrap = $nb.makeTag(wrap);

			var parts = wrap.split("></");

			if(parts.length >= 2) {

				return parts[0] + ">" + str + "</" + parts.splice(1).join("></");

			} else {

				parts = wrap.split(">", 2);

				if(parts.length == 2) {

					return wrap + str + "</" +
						(parts[0].split(" ")[0]).replace("<", "") +
					">";

				} else {

					return str;
				}
			}
		},

		/**
		 * Render either a Font Awesome or UIkit icon
		 *
		 * @param object $icon The icon data
		 * @return string
		 *
		 */
		icon: function($icon) {

			var out = "",
				icon = $icon["icon"] ? $icon["icon"] : $icon;

			switch(icon.substr(0, 2)) {

				case "uk":

					out += this.ukIcon(
						icon,
						$icon["ratio"]
					);

					break;

				case "fa":
				default:

					out += this.faIcon(
						icon,
						$icon["prefix"],
						$icon["classes"]
					);

					break;
			}

			return out;
		},

		/**
		 * Checks if a string is an html tag or not
		 *
		 * @param string str The string to be checked
		 * @return bool
		 *
		 */
		isTag: function(str) {
			return str.substr(0, 1) == "<" && str.substr(str.length - 1, 1) == ">";
		},

		/**
		 * Handle JSON requests
		 *
		 */
		json: {

			/**
			 * Variables
			 *
			 */
			data: {},
			limit: {},
			render: {},

			/**
			 * Init
			 *
			 */
			init: function() {

				$("[data-nb-json]").each(function() {
					$nb.json.get($(this));
				})

				this.renderItems()
			},

			/**
			 * Get
			 *
			 * @param jQuery $element
			 *
			 */
			get: function($element) {

				var $request = $element.data("nb-json"),
					$data = $request.data,
					$more = $element.children(".nb-json-more"),
					$message = $element.children(".nb-json-message"),
					id = $element.attr("id");

				if(!$more.length) {

					// Create more button
					$element.append(this.more());
					$more = $element.children(".nb-json-more")
				}

				// Add spinner
				$more.before(this.spinner());

				// Set button to loading
				var $btn = $more.children("button");

				$btn
					.html($nb.defaults.textLoading)
					.attr("disabled", true);

				// Init request data
				// Empty data is an array
				if($.isArray($data))
					$data = {};

				if(!this.data[id]) {

					// Add request data
					this.data[id] = $data;

					// Add start
					if(!this.data[id].start)
						this.data[id].start = 0;

					// Set init
					this.data[id].init = parseInt(this.data[id].start);

					// Set limit
					if(this.data[id].limit)
						this.limit = parseInt(this.data[id].limit);
				}

				// Request
				$.getJSON(
					($request.url ? $request.url : window.location.href),
					($request.more ? this.data[id] : $request.data),
					function($result) {

					$(".nb-json-spinner").remove();

					if($result.response == 200) {

						// Set button back to active
						$btn
							.html($nb.defaults.textLoad)
							.removeAttr("disabled");

						var $data = $result.message,
							$items = $data.items,
							itemType = $result.action;

						if($data.message) {

							if(!$message.length) {
								$element.prepend($nb.json.message());
								$message = $element.children(".nb-json-message");
							}

							$message.html($nb.ukAlert(
								"Showing " + ($data.count + $data.start - $nb.json.data[id].init) +
								" of " +
								($data.total - $nb.json.data[id].init) +
								" found",
								"primary",
								{close: false}
							))
						}

						if($.isFunction($nb.json.render[itemType])) {

							$data.config = $data.config == undefined ? {} : $data.config;
							$data.config.id = id;

							$more.before($nb.json.render[itemType]($items, $data.config));
							$nb.json.onRender($element, $result);

						} else {

							$element.html($nb.ukAlert("Sorry, the data could not be rendered.", "danger"));
						}

						if(!$data.count || $data.remaining === 0 || !$request.more) {

							// If no/all results have been found, hide button
							$more.hide();

						} else {

							// Set limit value
							if(!$nb.json.limit[id])
								$nb.json.limit[id] = parseInt($data.limit);

							// Set the new start value
							$nb.json.data[id].start = parseInt($nb.json.data[id].start) + $nb.json.limit[id];

							// Attach a single click event to the more button
							$element.off("click.nbJsonMore").one("click.nbJsonMore", ".nb-json-more", function() {
								$nb.json.get($element);
							});
						}

						$nb.json.onSuccess($element, $result);

					} else {

						$element.html($nb.ukAlert($result.message, "danger", {
							close: false
						}))
					}

					$nb.json.onReturn($element, $result);
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					$nb.json.onFail($element, jqXHR, textStatus, errorThrown);
				});
			},

			/**
			 * getJSON fail callback
			 *
			 * @param jQuery $element
			 * @param object jqXHR
			 * @param string textStatus
			 * @param string errorThrown
			 *
			 */
			onFail: function($element, jqXHR, textStatus, errorThrown) {

				$element.html($nb.ukAlert("Sorry, an error has occured. Please refresh this page.", "danger", {
					close: false
				}))
			},

			/**
			 * getJSON success callback, after result is rendered
			 *
			 * @param jQuery $element
			 * @param object $result
			 *
			 */
			onRender: function($element, $result) {},

			/**
			 * getJSON success callback, regardless of response code
			 *
			 * @param jQuery $element
			 * @param object $result
			 *
			 */
			onReturn: function($element, $result) {},

			/**
			 * getJSON success callback, if response code is 200
			 *
			 * @param jQuery $element
			 * @param object $result
			 *
			 */
			onSuccess: function($element, $result) {},

			/**
			 * Return the message div
			 *
			 */
			message: function() {
				return "<div class='nb-json-message uk-margin'></div>";
			},

			/**
			 * Return the more button
			 *
			 */
			more: function() {

				return $nb.htmlWrap(
					"<button type='button' class='uk-button uk-button-primary'>" + $nb.defaults.textLoad + "</button>",
					"<div class='nb-json-more uk-text-center uk-margin-large-top'>"
				);
			},

			/**
			 * Render items from a data attribute
			 *
			 */
			renderItems: function() {

				if(!$("[data-nb-json-render]").length)
					return;

				$("[data-nb-json-render]").each(function() {

					var $element = $(this),
						$result = $nb.base64toJSON($element.data("nb-json-render")),
						$data = $result.message,
						itemType = $result.action;

					if($result.response !== 200)
						return;

					if($.isFunction($nb.json.render[itemType])) {

						$data.config = $data.config == undefined ? {} : $data.config;
						$data.config.id = $element.attr("id");

						$element
							.html($nb.json.render[itemType]($data.items, $data.config))
							.removeAttr("data-nb-json-render");

						// Callbacks
						$nb.json.onRender($element, $result);
						$nb.json.onSuccess($element, $result);
						$nb.json.onReturn($element, $result);

					} else {

						$nb.json.onFail($element);
					}
				})
			},

			/**
			 * Render a loading spinner
			 *
			 */
			spinner: function() {

				return $nb.htmlWrap(
					$nb.ukSpinner(),
					"<div class='nb-json-spinner uk-text-center uk-margin'>"
				);
			}
		},

		/**
		 * Render a mailto link
		 * @return string
		 *
		 */
		mailto: function() {

			$("[data-nb-mailto]").each(function() {

				var $link = $(this),
					$data = $link.data("nb-mailto"),
					href = $data.id + "@" + $data.domain;

				// Set the href atte
				$link.attr("href", "m" + "a" + "i" + "l" + "t" + "o" + ":" + href);

				// Set link text
				$link.html(($data.text ? $data.text : href));

				// Set the icon if specified
				if($data["icon"])
					$link.before($nb.icon($data["icon"]) + " ");
			});
		},

		/**
		 * Makes a string an html tag if not already
		 *
		 * @param string str The string to be processed
		 * @return string
		 *
		 */
		makeTag: function(str) {
			return this.isTag(str) ? str : (str.substr(0, 1) == "<" ? "" : "<") + str + (str.substr(str.length - 1, 1) == ">" ? "" : ">");
		},

		/**
		 * Renders html attributes
		 *
		 * This returns `key=>value` pairs as html attributes.
		 * If no value is specified, or the value is `true`, a single attribute is assumed
		 * such as `checked` or `hidden`. If the value is an indexed array, this is imploded by a single
		 * space, which can be used for attributes with multiple values such as `class`.
		 * Associative array values will be returned as a JSON string.
		 *
		 * @param array $data The attributes to be rendered
	 	 * @param string tag The html element tag
		 * @param bool close Should the html tag be closed?
		 * @return string
		 *
		 */
		renderAttr: function($data, tag, close) {

			var a = [];

			for(var attr in $data) {

				var val = $data[attr];

				if(val !== false && val !== undefined) {

					if(val == "" || typeof val === "boolean") {
						a.push(attr);
					} else {

						if(Array.isArray(val)) {
							val = val.join(" ");
						} else if(typeof val === "object") {
							val = JSON.stringify(val);
						}

						a.push(attr + "='" + val + "'");
					}
				}
			}

			a = a.join(" ");
			a = a.length ? " " + a : "";

			if(tag)
				tag = tag.replace(/<|>|\//g, "");

			return tag ? "<" + tag + a + ">" + (close ? "</" + tag + ">" : "") : a;
		},

		/**
		 * Render a tel link
		 *
		 */
		tel: function() {

			$("[data-nb-tel]").each(function() {

				var $link = $(this),
					$data = $link.data("nb-tel");

				// Set the href attr
				$link.attr("href", "t" + "e" + "l" + ":" + $data.href);

				// Set link text
				$link.html(($data.text ? $data.text : $data.tel));

				// Set the icon if specified
				if($data["icon"])
					$link.before($nb.icon($data["icon"]) + " ");
			});
		},

		/**
		 * Render an alert
		 *
		 * @param string message The alert message
		 * @param string style The UIkit style modifier
		 * @param object $options Options to modify default behaviour:
		 * - `animation` (bool|string): Fade out or use the Animation component (default=true)
		 * - `close` (bool): Should a close button be displayed? (default=true)
		 * - `duration` (int): Animation duration in milliseconds (default=256)
		 * Please refer to the uk-alert documentation for all available options: https://getuikit.com/docs/alert#component-options
		 * @return string
		 *
		 */
		ukAlert: function(message, style, $options) {

			if($options == undefined)
				$options = {};

			// Set default options
			$options = $.extend({}, $nb.defaults.ukAlert.options, $options);

			var close = $options.close;
			delete $options.close;

			return $nb.htmlWrap(
				(close ? "<a class='uk-alert-close' data-uk-close></a>" : "") +
				"<p>" + message + "</p>",
				$nb.renderAttr({
					"class": "uk-alert-" + (style ? style : $nb.defaults.ukAlert.style),
					"data-uk-alert": $options
				}, "div")
			);
		},

		/**
		 * Return a UIkit arrow icon
		 *
		 * @param string dir The arrow direction
		 * @param int ratio The size of the icon
		 * @param string arrow The UIkit arrow icon to use
		 * @return string
		 *
		 */
		ukArrow: function(dir, ratio, arrow) {

			return this.ukIcon(
				(arrow ? arrow : this.defaults.arrow) + "-" + (dir === undefined ? "right" : dir),
				(ratio ? ratio : 1)
			);
		},

		/**
		 * Return a UIkit icon
		 *
		 * @param string icon The UIkit icon to return
		 * @param int ratio The size of the icon
		 * @return string
		 *
		 */
		ukIcon: function(icon, ratio) {

			return "<span uk-icon='" + JSON.stringify({
				icon: icon.replace("uk-", ""),
				ratio: (ratio ? ratio : 1)
			}) + "'></span>";
		},

		/**
		 * Returns a UIkit notification
		 *
		 * @param object $data The UIkit Notification data
		 *
		 */
		ukNotification: function($data) {

			if($.type($data) === "string")
				$data = {
					message: $data,
				};

			if($data.message) {

				// Set default attr
				$data = $.extend({}, $nb.defaults.ukNotification, $data);
				UIkit.notification($data);
			}
		},

		/**
		 * Display a UIkit spinner
		 *
		 * @param int ratio The size of the spinner
		 * @return string
		 *
		 */
		ukSpinner: function(ratio) {
			return "<div data-uk-spinner='ratio: " + (ratio ? ratio : 1) + "'></div>";
		},

		upload: {

			init: function() {

				if(!$("[data-nb-upload]").length)
					return;

				$("[data-nb-upload]").each(function() {

					var $item = $(this),
						$bar = $item.next("progress")[0],
						$btn = $item.closest("form").find("button[type=submit]"),
						$container = $item.prev("[data-nb-upload-items]"),
						$uploadItems = $container.data("nb-upload-items"),
						$config = $item.data("nb-upload-config"),
						$options = $.extend({

							beforeAll: function(env, files) {
								$btn.attr("disabled", true);
								$container.parent().children(".uk-alert").remove();
							},

							beforeSend: function(env) {
								// Set here as data-type currently does not work
								env.responseType = "json";
							},

							error: function(alert) {

								UIkit.modal.alert(alert);
								$bar.setAttribute("hidden", "hidden");
								$btn.attr("disabled", false);

								$nb.upload.onFail("upload", $item, alert);
							},

							fail: function(alert) {

								UIkit.modal.alert(alert);
								$btn.attr("disabled", false);

								$nb.upload.onFail("upload", $item, alert);
							},

							loadStart: function(e) {
								$bar.removeAttribute("hidden");
								$bar.max = e.total;
								$bar.value = e.loaded;
							},

							progress: function(e) {
								$bar.max = e.total;
								$bar.value = e.loaded;
							},

							loadEnd: function(e) {
								$bar.max = e.total;
								$bar.value = e.loaded;
							},

							complete: function() {

								var $result = arguments[0].response;

								if(typeof $result !== "object")
									$result = JSON.parse($result);

								if($result.response == 200) {

									var $uploads = $result.message.uploads,
										errors = $result.message.errors;

									// Render uploads
									$nb.upload.render($uploads, $container, $config, $item);

									// Render errors
									if(errors.length)
										for(var i = 0; i < errors.length; i++)
											$container.before($nb.ukAlert(errors[i], "danger"));

									$nb.upload.onSuccess("upload", $item, $result);

								} else {
									UIkit.modal.alert($result.message);
								}
							},

							completeAll: function() {

								$bar.setAttribute("hidden", "hidden");
								$btn.attr("disabled", false);
							}

						}, $item.data("nb-upload"));

					if(typeof $uploadItems === "object")
						$nb.upload.render($uploadItems, $container, $config);

					UIkit.upload($item, $options);
				});
			},

			description: function($container) {

				// Remove previously attached change event
				$container.off("change", "[data-nb-upload-description]");

				$container.on("change", "[data-nb-upload-description]", function() {

					var $input = $(this);

					$.post(window.location.href, $.extend({
						nbUpload: 1,
						nbUploadAction: "description",
						description: $input.val()
					}, $nb.upload.getItemData($input)), function($result) {

						var style = "success";

						if($result.response == 200) {

							$input.attr("data-nb-upload-description", $input.val());
							$nb.upload.onSuccess("description", $input, $result);

						} else {

							style = "danger";
							$input.val($input.data("nb-upload-description"))
							$nb.ukNotification({
								status: style,
								message: "Could not save description. Please refresh the page and try again."
							});
						}

						var cls = "uk-form-" + style;

						$input.addClass(cls);

						setTimeout(function() {
							$input.removeClass(cls);
						}, $nb.defaults.speed * 4);
					})
					.fail(function(jqXHR, textStatus, errorThrown) {
						$nb.upload.onFail("description", $input, errorThrown);
					});
				})
			},

			getItemData: function($element) {
				return $element.closest("[data-nb-upload-item]").data("nb-upload-item");
			},

			onFail: function(type, $element, message) {},

			onSuccess: function(type, $element, $result) {},

			remove: function($container) {

				// Remove previously attached click event
				$container.off("click", "[data-nb-upload-remove]");

				$container.on("click", "[data-nb-upload-remove]", function() {

					var $btn = $(this),
						$data = $.extend({
							nbUpload: 1,
							nbUploadAction: "remove",
						}, $nb.upload.getItemData($btn));

					UIkit.modal.confirm($data.confirm ? $data.confirm : "Are you sure you want to remove this upload?").then(function() {

						delete $data.confirm;

						$.post(window.location.href, $data, function($result) {

							if($result.response == 200) {

								var $upload = $btn.closest("[data-nb-upload-items]").next("[data-nb-upload]");

								if(!$upload.is(":visible"))
									$upload.slideDown($nb.defaults.speed);

								$btn.closest("[data-nb-upload-item]").slideUp($nb.defaults.speed, function() {
									$(this).remove();
								});

								$nb.upload.onSuccess("remove", $upload, $result);

							} else {

								UIkit.modal.alert($result.message);
							}
						})
						.fail(function(jqXHR, textStatus, errorThrown) {

							$nb.upload.onFail(
								"description",
								$btn.closest("[data-nb-upload-items]").next("[data-nb-upload]"),
								errorThrown
							);
						});

					}, function() {
						return false;
					});
				})
			},

			render: function($items, $container, $config, $item) {

				if($.isFunction($nb.json.render.upload) && Object.keys($items).length) {

					for(var filename in $items)
						$container.append($nb.json.render.upload($items[filename], $config));

					$nb.upload.remove($container);
					$nb.upload.description($container);
					$nb.upload.sort($container);

					if($item == undefined)
						$item = $container.next("[data-nb-upload]");

					if($config == undefined)
						$config = {};

					$config.multiple = $config.multiple == undefined ? $item.data("nb-upload").multiple : $config.multiple;

					// If not multiple, hide upload field
					if(!$config.multiple)
						$item.slideUp($nb.defaults.speed);
				}
			},

			sort: function($container) {

				if($container.hasClass("uk-sortable")) {

					UIkit.util.on($container, "moved", function(e) {

						var $items = [];
						$.each($container.find("[data-nb-upload-item]"), function(k, v) {
							$items.push($nb.upload.getItemData($(this)));
						});

						$.post(window.location.href, {
							nbUpload: 1,
							nbUploadAction: "sort",
							items: $items
						}, function($result) {

							$nb.ukNotification({
								status: ($result.response == 200 ? "success" : "danger"),
								message: $result.message
							});
						})
						.fail(function(jqXHR, textStatus, errorThrown) {
							$nb.upload.onFail("sort", $container, errorThrown);
						});
					});
				}
			}
		}
	};

	$(document).ready(function() {
		$nb.init();
	})

})(jQuery);
