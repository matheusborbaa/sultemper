(function ($) {
  "use strict";

  /*===========================================
        Table of contents
    01. On Load Function
    02. Preloader
    03. Mobile Menu
    04. Sticky fix
    05. Scroll To Top
    06. Set Background Image
    07. Popup Sidemenu
    08. Search Box Popup
    09. Counter Up Active
    10. Hero Slider Active
    11. Select Box Active
    12. Date & Time Picker
    13. Magnific Popup
    14. VS Button
    15. Isotope Active
    16. FAQ Parent Class Add
    17. Branch Information Toggler
    18. Quantity Added
    19. One Page Nav
    20. Rating Input Class Added
    21. Product Ship Another Toggle
    18. Right Click Disable
    19. Inspect Element Disable
    20. Google Map Active
    =============================================*/


  /*---------- 01. On Load Function ----------*/
  $(window).on('load', function () {
    $('.preloader').fadeOut('slow');
  });



/*---------- 02. Preloader ----------*/
  if ($('.preloader').length > 0) {
    $('.preloaderCls').each(function () {
      $(this).on('click', function (e) {
        e.preventDefault();
        $('.preloader').css('display', 'none');
      })
    });
  };



  /*---------- 03. Mobile Menu Active ----------*/
  $('.mobile-menu-active').vsmobilemenu({
    menuContainer: '.vs-mobile-menu',
    expandScreenWidth: 992,
    menuToggleBtn: '.vs-menu-toggle',
  });



  /*---------- 04. Sticky fix ----------*/
  var lastScrollTop = '';

  function stickyMenu($targetMenu, $toggleClass) {
    var st = $(window).scrollTop();
    if ($(window).scrollTop() > 600) {
      if (st > lastScrollTop) {
        // hide sticky menu on scroll down
        $targetMenu.removeClass($toggleClass);

      } else {
        // active sticky menu on scroll up
        $targetMenu.addClass($toggleClass);
      };
    } else {
      $targetMenu.removeClass($toggleClass);
    };

    lastScrollTop = st;
  };
  // Scroll To top Button Select 
  var scrollToTopBtn = '.scrollToTop'
  $(window).on("scroll", function () {
    stickyMenu($('.sticky-header'), "active");

    //Check to see if the window is top if not then display button
    if ($(this).scrollTop() > 400) {
      $(scrollToTopBtn).addClass('show');
    } else {
      $(scrollToTopBtn).removeClass('show');
    }

  });



  /*---------- 05. Scroll To Top ----------
  $(scrollToTopBtn).on('click', function (e) {
    e.preventDefault();
    $('html, body').animate({
      scrollTop: 0
    }, 3000);
    return false;
  }); */



  /*---------- 06.Set Background Image ----------*/
  if ($('.background-image').length > 0) {
    $('.background-image').each(function () {
      var src = $(this).attr('data-vs-img');
      $(this).css({
        'background-image': 'url(' + src + ')'
      });
    });
  };





  /*---------- 07. Popup Sidemenu ----------*/
  function popupSideMenu($sideMenu, $sideMunuOpen, $sideMenuCls, $toggleCls) {
    // Sidebar Popup
    $($sideMunuOpen).on('click', function (e) {
      e.preventDefault();
      $($sideMenu).addClass($toggleCls);
    });
    $($sideMenu).on('click', function (e) {
      e.stopPropagation();
      $($sideMenu).removeClass($toggleCls)
    });
    var sideMenuChild = $sideMenu + ' > div';
    $(sideMenuChild).on('click', function (e) {
      e.stopPropagation();
      $($sideMenu).addClass($toggleCls)
    });
    $($sideMenuCls).on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $($sideMenu).removeClass($toggleCls);
    });
  };
  popupSideMenu('.sidemenu-wrapper', '.sideMenuToggler', '.sideMenuCls', 'show');





  /*---------- 08. Search Box Popup ----------*/
  function popupSarchBox($searchBox, $searchOpen, $searchCls, $toggleCls) {
    $($searchOpen).on('click', function (e) {
      e.preventDefault();
      $($searchBox).addClass($toggleCls);
    });
    $($searchBox).on('click', function (e) {
      e.stopPropagation();
      $($searchBox).removeClass($toggleCls);
    });
    $($searchBox).find('form').on('click', function (e) {
      e.stopPropagation();
      $($searchBox).addClass($toggleCls);
    });
    $($searchCls).on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $($searchBox).removeClass($toggleCls);
    });
  };
  popupSarchBox('.popup-search-box', '.searchBoxTggler', '.searchClose', 'show');



  /*----------- 09. Counter Up Active ----------*/
  $('.counter').counterUp({
    delay: 10,
    time: 1000
  });



  /*----------- 10. Hero Slider Active ----------*/
  $('.vs-hero-carousel').each(function () {
    var vsHslide = $(this);

    function d(data) {
      return vsHslide.data(data)
    }
    vsHslide.layerSlider({
      allowRestartOnResize: true,
      maxRatio: (d('maxratio') ? d('maxratio') : 1),
      type: (d('slidertype') ? d('slidertype') : 'fullwidth'),
      pauseOnHover: (d('pauseonhover') ? true : false),
      navPrevNext: (d('navprevnext') ? true : false),
      hoverPrevNext: (d('hoverprevnext') ? true : false),
      hoverBottomNav: (d('hoverbottomnav') ? true : false),
      navStartStop: (d('navstartstop') ? true : false),
      navButtons: (d('navbuttons') ? true : false),
      loop: ((d('loop') == false) ? false : true),
      autostart: (d('autostart') ? true : false),
      height: (($(window).width() < 767) ? (d('sm-height') ? d('sm-height') : d('height')) : (d('height') ? d('height') : 1080)),
      responsiveUnder: (d('responsiveunder') ? d('responsiveunder') : 1220),
      layersContainer: ($(window).width() > 1920 ? (d('bigwidth') ? d('bigwidth') : d('container')) : (d('container') ? d('container') : 1220)),
      showCircleTimer: (d('showcircletimer') ? true : false),
      skinsPath: 'layerslider/skins/',
      thumbnailNavigation: ((d('thumbnailnavigation') == false) ? false : true)
    });

    vsHslide.find('[data-ls-go]').each(function () {
      $(this).on('click', function (e) {
        e.preventDefault();
        var target = $(this).data('ls-go');
        vsHslide.layerSlider(target)
      });
    });

  });






  /*----------- 11. Select Box Active ----------*/
  if($('select').length > 0){
    $('select').niceSelect();
  }
  


  /*---------- 12. Date & Time Picker ----------*/
  // Time And Date Picker
  $('.dateTime-pick').datetimepicker({
    timepicker: true,
    datepicker: true,
    format: 'y-m-d H:i',
    hours12: false,
    step: 30
  });

  // Only Date Picker
  $('.date-pick').datetimepicker({
    timepicker: false,
    datepicker: true,
    format: 'd/m/y',
    step: 10
  });

  // Only Time Picker
  $('.time-pick').datetimepicker({
    datepicker: false,
    timepicker: true,
    format: 'H:i',
    hours12: false,
    step: 10
  });



  /*----------- 13. Magnific Popup ----------*/
  /* magnificPopup img view */
  $('.popup-image').magnificPopup({
    type: 'image',
    gallery: {
      enabled: true
    }
  });

  /* magnificPopup video view */
  $('.popup-video').magnificPopup({
    type: 'iframe'
  });



  /*----------- 14. VS Button  ----------*/
  function buttonAnimation(btn) {
    $(btn).each(function () {
      var text = $(this).html();
      $(this).html('')
      $(this).prepend('<span class="btn-text">' + text + '</span><span class="btn-bg"></span>')
    })
    var effectElement = 'span.btn-bg';
    if ($(btn).length > 0) {
      $(btn).on('mouseenter', function (e) {
        var parentOffset = $(this).offset(),
          relX = e.pageX - parentOffset.left,
          relY = e.pageY - parentOffset.top;
        if ($(this).find(effectElement)) {
          $(this).find(effectElement).css({
            top: relY,
            left: relX,
          });
        }
      });
      $(btn).on('mouseout', function (e) {
        var parentOffset = $(this).offset(),
          relX = e.pageX - parentOffset.left,
          relY = e.pageY - parentOffset.top;
        if ($(this).find(effectElement)) {
          $(this).find(effectElement).css({
            top: relY,
            left: relX,
          });
        }
      });
    };
  };

  if ($('.vs-btn').length > 0) {
    buttonAnimation('.vs-btn');
  };






  /*---------- 15. Isotope Active ----------*/
  $('.filter-active').imagesLoaded(function () {
    var $grid = $('.filter-active').isotope({
      itemSelector: '.filter-item',
      percentPosition: true,
      masonry: {
        // use outer width of grid-sizer for columnWidth
        columnWidth: '.filter-item',
      }
    })

    // filter items on button click
    $('.filter-nav li').on('click', 'button', function () {
      var filterValue = $(this).attr('data-filter');
      $grid.isotope({
        filter: filterValue
      });
    });

  })

  if ($('.filter-nav').length > 0) {
    $('.filter-nav').append('<li class="indicator"></li>');
    if ($('.filter-nav li button').hasClass('active')) {
      let Left = $('.filter-nav li button.active').position().left + 'px',
        Width = $('.filter-nav li button.active').css('width');
      $('.indicator').css({
        left: Left,
        width: Width
      })
    }
    $('.filter-nav li button').on('click', function () {
      $('.filter-nav li button').removeClass('active');
      $(this).addClass('active');
      let cLeft = $('.filter-nav li button.active').position().left + 'px',
        cWidth = $('.filter-nav li button.active').css('width');
      $('.indicator').css({
        left: cLeft,
        width: cWidth
      })
    });
  }



  /*---------- 16. FAQ Parent Class Add ----------*/
  function faqClassAdd() {
    $('.faq-question').each(function () {
      $(this).on('click', function () {
        var wrapper = $(this).parent().parent();
        if (wrapper.hasClass('open')) {
          wrapper.removeClass('open')
        } else {
          $('.faq-card.open').removeClass('open')
          wrapper.addClass('open')
        }
      })
    });
  }
  faqClassAdd();



  /*---------- 17. Branch Information Toggler ----------*/
  function officerBoxToggler() {
    $('.officer-info-toggler').each(function () {
      $(this).on('click', function (e) {
        e.preventDefault();
        var box = $(this).next('.officer-info-box');
        box.toggleClass('show');
      })
    });
  }
  officerBoxToggler();


  /*---------- 18. Quantity Added ----------*/
  $('.quantity-plus').each(function () {
    $(this).on('click', function () {
      var $qty = $(this).siblings(".qty-input");
      var currentVal = parseInt($qty.val());
      if (!isNaN(currentVal)) {
        $qty.val(currentVal + 1);
      }
    })
  });

  $('.quantity-minus').each(function () {
    $(this).on('click', function () {
      var $qty = $(this).siblings(".qty-input");
      var currentVal = parseInt($qty.val());
      if (!isNaN(currentVal) && currentVal > 1) {
        $qty.val(currentVal - 1);
      }
    });
  })


  /*----------- 19. One Page Nav ----------*/
  function onePageNav(element) {
    if ($(element).length > 0) {
      $(element).each(function () {
        $(this).find('a').each(function () {
          $(this).on('click', function () {
            var target = $(this.getAttribute('href'));
            if (target.length) {
              event.preventDefault();
              $('html, body').stop().animate({
                scrollTop: target.offset().top - 10
              }, 1000);
            };

          });
        });
      })
    }
  };
  onePageNav('.onepage-nav');



  /*----------- 20. Rating Input Class Added ----------*/
  if ($('.vs-rating-input').length > 0) {
    $('.vs-rating-input').each(function () {
      $(this).find('span').on('click', function () {
        $('.vs-rating-input span').addClass('active');
        $(this).nextAll('span').removeClass('active');
      });
    });
  };



  /*----------- 21. Product Ship Another Toggle ----------*/
  $('#buyerShipAnother').on('click', function (e) {
    e.preventDefault();
    $('.vs-billing-differentAddress').toggle();
  });




  /*----------- 18. Right Click Disable ----------*/
  // window.addEventListener('contextmenu', function (e) {
  //   // do something here... 
  //   e.preventDefault();
  // }, false);


  /*----------- 19. Inspect Element Disable ----------*/
  // document.onkeydown = function (e) {
  //   if (event.keyCode == 123) {
  //     return false;
  //   }
  //   if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
  //     return false;
  //   }
  //   if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
  //     return false;
  //   }
  //   if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
  //     return false;
  //   }
  //   if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
  //     return false;
  //   }
  // }





  /*----------- 20. Google Map Active ----------*/
  function contactMap() {
    // Basic options for a simple Google Map
    // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
    var mapOptions = {
      // How zoomed in you want the map to start at (always required)
      zoom: 11,
      scrollwheel: false,
      // The latitude and longitude to center the map (always required)
      center: new google.maps.LatLng(40.6700, -73.9400), // New York
      // This is where you would paste any style found on Snazzy Maps.
      styles: [{
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [{
          "color": "#e9e9e9"
        }, {
          "lightness": 17
        }]
      }, {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [{
          "color": "#f5f5f5"
        }, {
          "lightness": 20
        }]
      }, {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [{
          "color": "#ffffff"
        }, {
          "lightness": 17
        }]
      }, {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [{
          "color": "#ffffff"
        }, {
          "lightness": 29
        }, {
          "weight": .2
        }]
      }, {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [{
          "color": "#ffffff"
        }, {
          "lightness": 18
        }]
      }, {
        "featureType": "road.local",
        "elementType": "geometry",
        "stylers": [{
          "color": "#ffffff"
        }, {
          "lightness": 16
        }]
      }, {
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [{
          "color": "#f5f5f5"
        }, {
          "lightness": 21
        }]
      }, {
        "featureType": "poi.park",
        "elementType": "geometry",
        "stylers": [{
          "color": "#dedede"
        }, {
          "lightness": 21
        }]
      }, {
        "elementType": "labels.text.stroke",
        "stylers": [{
          "visibility": "on"
        }, {
          "color": "#ffffff"
        }, {
          "lightness": 16
        }]
      }, {
        "elementType": "labels.text.fill",
        "stylers": [{
          "saturation": 36
        }, {
          "color": "#333333"
        }, {
          "lightness": 40
        }]
      }, {
        "elementType": "labels.icon",
        "stylers": [{
          "visibility": "off"
        }]
      }, {
        "featureType": "transit",
        "elementType": "geometry",
        "stylers": [{
          "color": "#f2f2f2"
        }, {
          "lightness": 19
        }]
      }, {
        "featureType": "administrative",
        "elementType": "geometry.fill",
        "stylers": [{
          "color": "#fefefe"
        }, {
          "lightness": 20
        }]
      }, {
        "featureType": "administrative",
        "elementType": "geometry.stroke",
        "stylers": [{
          "color": "#fefefe"
        }, {
          "lightness": 17
        }, {
          "weight": 1.2
        }]
      }]
    };
    // Get the HTML DOM element that will contain your map 
    // We are using a div with id="map" seen below in the <body>
    var mapElement = document.getElementById('google-map');

    // Create the Google Map using our element and options defined above
    var map = new google.maps.Map(mapElement, mapOptions);

    // Let's also add a marker while we're at it
    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(40.6700, -73.9400),
      map: map,
      title: 'Cryptox'
    });
  }
  if ($('#google-map').length != 0) {
    google.maps.event.addDomListener(window, 'load', contactMap);
  }



















})(jQuery);