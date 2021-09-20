/*!
 jQuery UIKit Select
 @name uikit-select.js
 @author Milos Djordjevic (milos@themelaboratory.com)
 @version 1.0
 @date 01/04/2018
 @category jQuery Plugin
 @copyright (c) 2018 [Docodes/Milos Djordjevic]
 @license Licensed under the MIT license
 */
;(function ($, window, document, undefined) {
    'use strict';


    // Check if namespace has already been initialized
    if (!$.doo) {
        $.doo = {};
    }


    /**
     * Real magic happends here :D
     *
     * @param elements
     * @param options
     */
    $.doo.UiKitSelect = function (el, options) {


        var base = this;


        // Access to jQuery and DOM versions of  element
        base.$el = $(el);
        base.el = el;


        /* Add a reverse reference to the DOM object */
        base.$el.data("UiKitSelect", base);


        /* ---------------------------------------------------------------------------- */
        /* Plugin default options ----------------------------------------------------- */
        /* ---------------------------------------------------------------------------- */
        var defaults = {

            multiple: false,
            toggle_icon: "icon-arrow-line-down",
            container_class: "uk-select-wrapper",
            item_class: "uk-option",
            label_class: "uk-label",
            placeholder: "Select",

            // EVent Handlers

            onInit: function () {
            },
            onDestroy: function () {
            }
        };


        /* ---------------------------------------------------------------------------- */
        /* Private Methods ------------------------------------------------------------ */
        /* ---------------------------------------------------------------------------- */


        function _nativeGetSelected() {
            return {"value": base.$el.val(), "index": base.$el.prop("selectedIndex"), "name": base.$el.children(":selected").text()}
        }


        /**
         *
         */
        function _renderDropdown() {

            var btn, dd, value, markup, is_selected;

            is_selected = dd = "";

            // selected value or first value is nothing selected

            btn = '<button type="button" class="uk-button uk-button-block uk-button-select"><span>' + base.options.current.name + '</span><i class="' + base.options.toggle_icon + '"></i></button>';

            // Iterate trough otions and build dropdown
            base.$el.children().each(function (i, o) {

                if ($(this).index() == base.options.current.index) {
                    is_selected = "selected"
                }else{
                    is_selected = ""
                }

                dd += '<li class="' + base.options.item_class + " " + is_selected + '" data-index="' + $(this).index() + '" data-value="' + $(this).attr("value") + '" data-lbl="' + $(this).text() + '">' + $(this).text() + '</li>';
            });

            dd = "<div class='dd'><ul>" + dd + "</ul></div>";

            markup = $("<div class='" + base.options.container_class + "'>" + btn + dd + "</div>");

            base.options.new_el_ref = markup;

            markup.insertAfter(base.$el);
        }


        /**
         *
         */
        function _bindEvents() {

            var dd = base.options.new_el_ref;

            // Add dropdown
            UIkit.dropdown(dd.find(".dd"), {
                offset: 5,
                delayHide: 0,
                animation: "uk-animation-slide-bottom-small"
            });

            // Close DD is user clicks on option item

            dd.find(".uk-option").on("click", function () {

                var $that = $(this),
                    data = { "index": $that.data("index"), "name": $that.data("lbl")};

                // Update native control
                base.update(data);

                console.log($that);

                $that.parent().find(".uk-option").removeClass("selected");
                $that.addClass("selected");


                // Close DD
                UIkit.dropdown($(this).parents(".dd")).hide();

            });
        }


        /**
         *
         */
        function _hideNativeSelect() {
            base.$el.addClass("hidden-selectbox");
        }


        /* --------------------------------------------------------------------------- */
        /* Public Methods ------------------------------------------------------------ */
        /* --------------------------------------------------------------------------- */


        /**
         * Callback hooks.
         * Usage: In the defaults object specify a callback function:
         * hookName: function() {}
         * Then somewhere in the plugin trigger the callback:
         * hook('hookName');
         */
        base.hook = function (hookName) {
            if (base.options[hookName] !== undefined) {
                // Call the user defined function.
                // Scope is set to the jQuery element we are operating on.
                base.options[hookName].call(el);
            }
        }


        /**
         * Initialize plugin
         *
         * @fix Maybe this should be moved assigned to private methods
         */
        base.init = function () {

            // Extend default options with those supplied by user.
            base.options = $.extend({}, defaults, options);

            // Set current/selected option
            base.options["current"] = _nativeGetSelected();

            _renderDropdown();
            _bindEvents();

            _hideNativeSelect();
            base.hook("init");
        }


        // Run initializer
        base.init();


        /**
         * Updates native control
         */
        base.update = function (data) {
            base.$el.prop('selectedIndex', data.index).change();
            base.options.new_el_ref.find(".uk-button span").html(data.name);
        }


        /**
         *
         */
        base.destroy = function () {
            base.hook("destroy");
        }
    };


    /**
     * Extend the $.fn object
     *
     * @param options
     * @constructor
     */
    $.fn.UiKitSelect = function (options) {
        return this.each(function () {
            if (!$.data(this, "UiKitSelect")) {
                new $.doo.UiKitSelect(this, options);
            }
            else if ($.isFunction($.doo.UiKitSelect[options])) {
                $.data(this, "UiKitSelect")[options]();
            }
        });
    };

})(jQuery, window, document);