//  Fuck it I'm putting label overrides here
//  - matches on desktop label
//  - swaps out value of labels to mobile label when size is small enough
//  - hides/shows on load or resize
//  - needs to be explicitly referenced by doMenuOverrides/initMenuOverrides (see below)
const secondary_menu_overrides = [
	{
		mobile_label: "Trains",
		desktop_label: "Train times ➚"
	},
	{
		mobile_label: "Weather",
		desktop_label: "Weather, surf ➚"
	},
	{
		mobile_label: "Radio",
		desktop_label: "Local radio ➚"
	},
	{
		mobile_label: "Forums",
		desktop_label: "Forums ➚"
	},
	{
		mobile_label: "Official Stuff",
		desktop_label: "Official Stuff",
		display: "desktop-only"
	}
]



/**
 * Add any custom theme JavaScript to this file.
 */

function adjustTopImage($) {

	var topBar = $('.site-header');
	var media = $('.top-home .widget_media_image');
	var bottomOfTopBar = topBar.outerHeight() + $(window).scrollTop();
	var bottomOfMediaImage = media.outerHeight() + media.offset().top;
	var topSecondaryNav = $('.nav-secondary').offset().top;

	var secondNavDirectlyInBody = $('.nav-secondary').parent().is('body');
	if (bottomOfTopBar >= topSecondaryNav && secondNavDirectlyInBody) {
		$('.nav-secondary').detach().appendTo(".site-header");
		var contentPlacement = $('header.site-header').position().top + $('header.site-header  ').height();
		$('header.site-header').next().css('margin-top', contentPlacement);
	}

	if (bottomOfMediaImage >= bottomOfTopBar && !secondNavDirectlyInBody) {
		$('.nav-secondary').detach().insertBefore(".site-inner");
		var contentPlacement = $('header.site-header').position().top + $('header.site-header  ').height();
		$('header.site-header').next().css('margin-top', contentPlacement);
	}
}


function adjustAToZSearchBar($) {
	var topBar = $('.site-header');
	var searchBar = $('.a_to_z_searchbar');
	var widget = $("div#a_to_z_widget");

	var bottomOfTopBar = topBar.outerHeight() + $(window).scrollTop();
	var topOfWidget = widget.offset().top;
	var searchBarInWidget = $('.a_to_z_searchbar').parent().is(widget);
	if (bottomOfTopBar >= topOfWidget && searchBarInWidget) {
		searchBar.detach().appendTo(".site-header");
		widget.toggleClass('extra-padding');
	}

	if (bottomOfTopBar < topOfWidget && !searchBarInWidget) {
		searchBar.detach().insertBefore(".a_to_z_jumplinks");
		widget.toggleClass('extra-padding');
	}

	// for mobile.. we fix at top of screen
	var widgetInWindowTop = widget.offset().top - $(window).scrollTop();
	var searchBarInWindowTop = searchBar.offset().top - $(window).scrollTop();
	if (widgetInWindowTop <= 1 && searchBarInWindowTop <= 0) {
		if (!searchBar.hasClass("fixed-at-top"))
			searchBar.addClass("fixed-at-top");
	}
	else {
		searchBar.removeClass("fixed-at-top");
	}
	
}


/**
 * Functions to handle mihi controls
 */
function handleMihiAudio($) {
	var audio = $('.audio');
	var volume = audio.find('.audio_icon');
	var player = audio.find('#mihi');
	if (audio && volume && player) {
		audio.on("click", "button",
			function () {
				toggle();
		});
		player.on("ended",
			function () {
				reset();
		});
		function toggle() {
			if( volume.hasClass('fa-volume-up') ) {
				volume.removeClass('fa-volume-up').addClass('fa-volume-off');
				player.trigger('play');
			} else {
				volume.removeClass('fa-volume-off').addClass('fa-volume-up');
				player.trigger('pause');
			}
		};
		function reset() {
			volume.removeClass('fa-volume-off').addClass('fa-volume-up');
			player.prop("currentTime",0);
		};
	}
}

function fixPaekakarikiSpelling($) {
	$("p").each(function () {
		$(this).html($(this).html().replace(/Paekakariki/g, "Paekākāriki"));
	});
}

function initializeMenuOverrides($, menuSelector, menuContent) {
	$(menuSelector + " li.menu-item").each(function () {
		var matched;
		var current_label = $(this).find("a span").first().text();
		var matched = _.find(menuContent, function (item) {
			// assume that menus are set to the desktop label by default
			return (item.desktop_label.match(new RegExp( current_label, "i")));
		});
		//console.log("Found in menu overrides ", matched);
		if (matched) {
			var this_menu_id = $(this).attr('id');
			matched.menu_item_id = this_menu_id;
		}
	});
}

function doMenuOverrides($, menuSelector, menuContent) {
	$(menuSelector + " li.menu-item").each(function () {
		var this_menu_id = $(this).attr('id');
		var matched = _.findWhere(menuContent, { menu_item_id: this_menu_id });
		if (matched) {
			if (!window.matchMedia("(min-width: 1152px)").matches) {
				$(this).find("a span").each(function () {
					$(this).html(matched.mobile_label);
				});

				if (matched.display === "mobile-only")
					$(this).show();

				if (matched.display === "desktop-only")
					$(this).hide();

			} else {
				$(this).find("a span").each(function () {
					$(this).html(matched.desktop_label);
				});

				if (matched.display === "mobile-only") 
					$(this).hide();

				if (matched.display === "desktop-only")
					$(this).show();

			}
		}
	});
}

function hackMenusForMobile($) {
	if (!window.matchMedia("(min-width: 896px)").matches) {
		$('#genesis-mobile-nav-secondary').hide();
		$('.nav-primary .quadmenu-navbar-header').hide();
		$('.nav-secondary .quadmenu-navbar-header').hide();
		$('.nav-secondary .quadmenu-container div.quadmenu-navbar-collapse').addClass("in");
	}
}

function fixTopBannerPlacement($) {

	if (window.matchMedia("(min-width: 896px)").matches) {
		var contentPlacement = $('header.site-header').position().top + $('header.site-header').height();
		$('header.site-header').next().css('margin-top', contentPlacement);

		if ($(".top-home img").length > 0) {
			// if we're on the front-home page
			$('.nav-secondary').detach().insertBefore(".site-inner");
			var contentPlacement = $('header.site-header').position().top + $('header.site-header  ').height();
			$('header.site-header').next().css('margin-top', contentPlacement);
			$('top-home').css('margin-top', 0);
			$(window).scroll(function () { adjustTopImage($) });
			$(window).resize(function () { adjustTopImage($) });
		}
		else {
			var contentPlacement = $('.site-container header').position().top + $('.site-container header').height();
			$('.site-inner').css('margin-top', contentPlacement);
			$('top-home').css('margin-top', 0);
		}
	}
}

(function (document, $) {

	'use strict';

	if ($("#a_to_z_widget").length > 0) {
		adjustAToZSearchBar($);
		$(window).scroll(function () { adjustAToZSearchBar($) });
		$(window).resize(function () { adjustAToZSearchBar($) });
	}

	$(function () {
		initializeMenuOverrides($, ".nav-primary", secondary_menu_overrides);
		doMenuOverrides($, ".nav-primary", secondary_menu_overrides);
		fixTopBannerPlacement($);
		handleMihiAudio($);
		fixPaekakarikiSpelling($);
		hackMenusForMobile($);

		$(window).resize(function () {
			doMenuOverrides($, ".nav-primary", secondary_menu_overrides);
		});
	});


	
} )( document, jQuery );
