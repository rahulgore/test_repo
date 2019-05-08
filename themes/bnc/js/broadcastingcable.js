jQuery(function() {

  var $ddt = jQuery('.navbar-main .dropdown-toggle'),
    $ddm = jQuery('.navbar-main .dropdown-menu'),
    $nvb = jQuery('#navbar'),
    $bts = jQuery('.btn-search');

  $bts.on('mouseenter', function() {
      // hide other dropdowns
      jQuery('.navbar-main').find('.navbar-nav > li.open').removeClass('open').parent('ul').removeClass('open-ul');
  });

  $ddt.on('mouseenter', function() {
    var $this = jQuery(this);

    // IF NOT MOBILE: 
    if ( !$nvb.hasClass('in') ) {

      // open dropdown and close other dropdowns
      $this.parent('li').addClass('open').siblings('li').removeClass('open');

      $this.parent('li').on('mouseleave', function() {
          // close dropdown
          $this.parent('li').removeClass('open').parents('ul').removeClass('open-ul');
      });
      
      // Add .open-ul to parent ul to hide siblings (mobile)
      $this.parents('ul').first().addClass('open-ul');

      // hide dropdown if clicked elsewhere
      jQuery('body').on('click', function(e) {
        if ( !jQuery(e.target).parents('#navbar').hasClass('navbar-collapse') ) {
          $this.parent('li').removeClass('open').parents('ul').removeClass('open-ul');
        }
      });

    } else {

    }
  });

  $ddt.on('mouseleave', function() {
    var $this = jQuery(this);
    
    // if no dropdown-elements, remove highlight
    if ( !$this.next('.dropdown-menu') ) {
      $this.parent('li').removeClass('open').parents('ul').removeClass('open-ul');
    }
  });
    
  $ddt.on('click', function() {
    var $this = jQuery(this);

    // Toggle dropdown
    $this.parent('li').toggleClass('open');

    // Toggle .open-ul to parent ul to hide siblings (mobile)
    $this.parents('ul').first().toggleClass('open-ul');

  });

  $ddm.on('mouseleave', function() {
    var $this = jQuery(this);

    // IF NOT MOBILE: 
    if ( !$nvb.hasClass('in') ) {
      // Close dropdown
      // $this.parent('li').removeClass('open');
      // $this.parents('ul').first().removeClass('open-ul');
    }

  });

  jQuery('.dropdown-tab-nav').find('a[data-toggle=tab]').on('mouseenter', function() {
    var $this = jQuery(this);

    // IF NOT MOBILE: 
    if ( !$nvb.hasClass('in') ) {
      // Add active class and remove on siblings
      $this.parent('li').addClass('active').siblings('li.active').removeClass('active');
    }
  });

  jQuery('.dropdown-tab-nav').find('a[data-toggle=tab]').on('mouseenter mouseleave', function() {
    var $this = jQuery(this);

    // IF NOT MOBILE: 
    if ( !$nvb.hasClass('in') ) {
      // Remove inactive class in .dropdown-tabs (desktop)
      // Hide upper level li.open (mobile)
      $this.parents('.dropdown-tabs').first().removeClass('inactive').toggleClass('open-tabs').siblings('.dropdown-toggle').toggleClass('hidden-xs');

      // Remove other .active class (for multilevel ul's)
      jQuery( $this.data('target') ).addClass('active').siblings('.active').removeClass('active');
    }
  });

  jQuery('.dropdown-tab-nav').find('a[data-toggle=tab]').on('click', function(e) {
    var $this = jQuery(this);

    // IF MOBILE: 
    if ( $nvb.jQueryhasClass('in') ) {
      e.preventDefault();
      // Remove inactive class in .dropdown-tabs (desktop)
      // Hide upper level li.open (mobile)
      $this.parents('.dropdown-tabs').first().removeClass('inactive').toggleClass('open-tabs').siblings('.dropdown-toggle').toggleClass('hidden-xs');

      // Remove other .active class (for multilevel ul's)
      jQuery( $this.data('target') ).addClass('active').siblings().removeClass('active');
    }
  });

  // WINDOW RESIZE LISTENER
  // for masthead elements
  var sizeTimer,
    prevWinWidth = window.innerWidth;

  jQuery(window).on('resize', function() {
    clearTimeout(sizeTimer);
    sizeTimer = setTimeout(function() {
      // change navbar stuff
      navbar_menu_height( $navbar_main, $navbar_main_collapse );

      // change content order stuff
      if ( window.innerWidth < NARROW_MAX_WIDTH ) {
        // change the thing only if breakpoint changed
        if ( prevWinWidth >= NARROW_MAX_WIDTH ) {
          arrange_content_mobile();
        }
      } else {
        // change the thing only if breakpoint changed
        if ( prevWinWidth < NARROW_MAX_WIDTH ) {
          arrange_content_desktop();
        }
      }

      prevWinWidth = window.innerWidth;    
    }, 300);
  });


  // Adjust height of navbar-articles box for mobile
  var $navbar_main = jQuery('.navbar-main'),
    $navbar_main_collapse = jQuery('.navbar-main-collapse');

  $navbar_main_collapse.on('shown.bs.collapse', function() {
    navbar_menu_height( $navbar_main, $navbar_main_collapse );
  });

  function navbar_menu_height( $navbar_main, $navbar_main_collapse ) {
    if ( $navbar_main.hasClass('affix') ) {
      $navbar_main_collapse.height( window.innerHeight - $navbar_main.height() );
    } else {
      var scrollTop     = jQuery(window).scrollTop(),
        elementOffset = $navbar_main.offset().top,
        distance      = (elementOffset - scrollTop);

      $navbar_main_collapse.height( window.innerHeight - distance - $navbar_main.height() );    
    }
  }

  // Reorder positions of content blocks for mobile 
  var $banner_300_side_1  = jQuery('#banner-300-s1'),
    $banner_300_main_2  = jQuery('#banner-300-m2'),
    $blogs_col_ad       = jQuery('#blogs-col-ad'),
    $events_carousel    = jQuery('#events-carousel'),
    $wire_carousel      = jQuery('#wire-carousel'),
    $resources_carousel = jQuery('#resources-carousel'),
    $talkback           = jQuery('#widget-talkback'),
    $schmooze           = jQuery('#section-schmooze'),
    $currentissue       = jQuery('#widget-currentissue');

  function arrange_content_mobile() {
    jQuery( $banner_300_side_1, $banner_300_main_2, $wire_carousel, $resources_carousel ).detach();

    $banner_300_main_2.insertAfter( $events_carousel );
    $wire_carousel.insertBefore( $talkback );
    $resources_carousel.insertBefore( $talkback );

    // if homepage, put #banner-300-s1 after top headlines
    // else put after #currentissue 
    var $content            = jQuery('#content'),
      $topheadlines_list  = jQuery('#topheadlines-list');
    if ( $content.hasClass('content-index') ) {
      $banner_300_side_1.insertAfter( $topheadlines_list );
    } else {
      $banner_300_side_1.insertAfter( $currentissue );
    }
  }

  function arrange_content_desktop() {
    jQuery( $banner_300_side_1, $banner_300_main_2, $wire_carousel, $resources_carousel ).detach();

    $blogs_col_ad.append( $banner_300_main_2 );
    $wire_carousel.insertBefore( $schmooze );
    $resources_carousel.insertBefore( $schmooze );
  }

  var NARROW_MAX_WIDTH = 991;
  jQuery(document).ready(function() {
    if ( window.innerWidth < NARROW_MAX_WIDTH ) {
      arrange_content_mobile();
    } else {
      arrange_content_desktop();
    }
  });
});