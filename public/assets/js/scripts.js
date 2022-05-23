(function($) {
    var allfunction = {

        //============== Menu Icon ==============
        menu_icon: function() {
            $('.main-menu ul li').each(function() {
                if ($(this).children('.sub-menu ,.mega-menu').length) {
                    $(this).children('a').append('<i class="icofont-rounded-down"></i>');
                }
            });
        },

        //=============== Menu ==============
        menu_toggle: function(){
            $('.hamburger').on("click", function(e) {
                e.stopPropagation()
                $('.main-menu').toggleClass('active');
                if ($(".main-menu").hasClass('active')) {
                    $('.header-account').removeClass('active');
                    $("body").addClass('overlay');
                } else {
                    $("body").removeClass('overlay');
                    $(".plate").removeClass('active');
                }
            });
            $('.main-menu ul li').click(function(e) {
                e.stopPropagation();
                $(this).toggleClass('active').children('.sub-menu ,.mega-menu').toggleClass('active').parent('li').siblings().removeClass('active').children().removeClass('active');
            });
        },

        // ===================== Mega Menu ===============
        mega_menu: function() {
            $('.single-menu .title').click(function(e) {
            e.stopPropagation();
                $(this).toggleClass('active').next().toggleClass('active').parents().siblings().children().removeClass('active');
            });
        },

        // ============== Hamburger icon ==================
        hamburger_icon: function() {
            $('.plate').click(function() {
                $(this).toggleClass('active');
            });
        },

        // ============== User Icon ==================
        user_toogle: function(){
            $('.user-icon').on("click", function(e) {
                e.stopPropagation();
                $('.header-account').toggleClass('active');
                if ($(".header-account").hasClass('active')) {
                    $('.main-menu').removeClass('active');
                    $(".plate").removeClass('active');
                    $("body").addClass('overlay');
                  } else {
                    $("body").removeClass('overlay');
                  }
            });
        },

        //================== Search Toogle ======================
        search_toogle: function(){
            $('.search-button').on("click", function(e) {
                e.stopPropagation();
                $('.search-popup-wrapper').addClass('active');
                $("body").addClass('overlay');
                $('.main-menu').removeClass('active');
                $(".header-account").removeClass('active')
                $(".plate").removeClass('active');
            });
            $('.search-popup-close').on("click", function(e) {
                e.stopPropagation();
                $('.search-popup-wrapper').removeClass('active');
                $("body").removeClass('overlay');
            });
        },

        //============= Counter Up ===============
        counter_up: function(){
            $('.odometer').appear(function (e) {
                var odo = $(".odometer");
                odo.each(function () {
                    var countNumber = $(this).attr("data-count");
                    $(this).html(countNumber);
                });
            });
        },

        // Popup Gallery
        popup_gallery: function() {
            $('.popup-image').magnificPopup({
                type: 'image',
                gallery: {
                  enabled: true
                }
            });
        },

        // Popup Video
        popup_video: function() {
            $('.popup-video').magnificPopup({
                type: 'iframe'
            });
         },


        // Gallery V1 Isotop
        gallery_v1_isotop: function() {
            if ($('.gallery-v1-wrapper').length) {
                // init Isotope
                var $grid = $('.gallery-v1-wrapper').isotope({
                    itemSelector: '.isotop-item',
                    percentPosition: true,
                    masonry: {
                        columnWidth: '.grid-sizer',
                    }
                });
                $grid.imagesLoaded().progress(function() {
                    $grid.isotope('layout')
                });
            }
        },

        // Gallery V2 Isotop
        gallery_v2_isotop: function() {
            if ($('.isotop-active').length) {
                // init Isotope
                var $grid = $('.isotop-active').isotope({
                    itemSelector: '.isotop-item-v2',
                    percentPosition: true,
                    masonry: {
                        columnWidth: '.grid-sizer-v2',
                    }
                });
                // filter items on button click
                $('.filter-button-group').on( 'click', 'button', function() {
                    var filterValue = $(this).attr('data-filter');
                    $grid.isotope({ filter: filterValue });
                    $(this).addClass('active').siblings().removeClass('active');
                });

                $grid.imagesLoaded().progress(function() {
                    $grid.isotope('layout')
                });
            }
        },

        // Count Down Clock
        countdown_clock: function(){
            // jQuery CountDown
            if( $(".countdown-clock").length ){
                $('.countdown-clock').countdown('2022/07/18', function(event) {
                    $('.clock-days').html(event.strftime('%D'));
                    $('.clock-hours').html(event.strftime('%H'));
                    $('.clock-minutes').html(event.strftime('%M'));
                    $('.clock-seconds').html(event.strftime('%S'));
                });
            }
        },

        //================ Accordion =================
        accordion: function() {
            $('.single-faq').click(function() {
                if ($(this).hasClass('active')){
                    $(this).removeClass("active").children('.faq-body').slideUp();
                }else{
                    $('.faq-v1-wrapper , .faq-v2-wrapper').find('.single-faq').removeClass('active').children('.faq-body').slideUp();
                    $(this).addClass("active").children('.faq-body').slideDown();
                }
            });
        },

        // Nice Select
        nice_select: function(){
            $('.select-active').niceSelect();
         },

         // Landing Gallery v1  Isotop
        landing_v1_gallery_isotop: function() {
            if ($('.landing-v1-gallery-wrapper').length) {
                // init Isotope
                var $grid = $('.landing-v1-gallery-wrapper').isotope({
                    itemSelector: '.isotop-item',
                    percentPosition: true,
                    masonry: {
                        columnWidth: '.grid-sizer',
                    }
                });
                $grid.imagesLoaded().progress(function() {
                    $grid.isotope('layout')
                });
            }
        },


         // Landing Gallery v1  Isotop
        landing_v2_gallery_isotop: function() {
            if ($('.landing-v2-gallery-wrapper').length) {
                // init Isotope
                var $grid = $('.landing-v2-gallery-wrapper').isotope({
                    itemSelector: '.isotop-item',
                    percentPosition: true,
                    masonry: {
                        columnWidth: '.grid-sizer',
                    }
                });
                $grid.imagesLoaded().progress(function() {
                    $grid.isotope('layout')
                });
            }
        },

        // =============== All Slider Here =============

        // ------------- Partner Slider ----------------
        partner_slider: function() {
            $('.partner-slider-wrapper').owlCarousel({
                loop:false,
                margin:10,
                dots: true,
                nav:false,
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:true,
                responsive:{
                    0: {
                        items:2
                    },
                    600:{
                        items:3
                    },
                    1000:{
                        items:4
                    }
                }
            })
        },

        // ------------- Upcoming Event Slider ----------------
        upcoming_event_slider: function() {
            $('.upcome-slider-active').owlCarousel({
                loop:true,
                margin:10,
                dots: true,
                nav:false,
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:true,
                items:1
            })
        },

        // ------------- Speaker Slider ----------------
        meet_speaker_slider: function() {
            $('.speaker-slider-active').owlCarousel({
                loop:true,
                margin:10,
                dots: true,
                nav:false,
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:true,
                responsive:{
                    0: {
                        items:1
                    },
                    600:{
                        items:2
                    },
                    1000:{
                        items:3
                    }
                }
            })
        },

        // ------------- Sponsor V1 Slider ----------------
        sponsor_slider_home_v1: function() {
            $('.sponsor-slider-v1-active').owlCarousel({
                loop:true,
                margin:10,
                dots: true,
                nav:false,
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:true,
                responsive:{
                    0: {
                        items:2
                    },
                    600:{
                        items:3
                    },
                    1000:{
                        items:4
                    }
                }
            })
        },

        // ------------- Speaker Slider ----------------
        meet_speaker_slider: function() {
            $('.speaker-slider-active').owlCarousel({
                loop:true,
                margin:10,
                dots: true,
                nav:false,
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:true,
                responsive:{
                    0: {
                        items:1
                    },
                    600:{
                        items:2
                    },
                    1000:{
                        items:3
                    }
                }
            })
        },

        // ------------- Home v2 Speaker Slider ----------------
       home_v2_meet_speaker_slider: function() {
            $('.home-v2-meet-speaker-active').owlCarousel({
                loop:true,
                margin:0,
                dots: true,
                nav:false,
                responsive:{
                    0: {
                        items:1
                    },
                    600:{
                        items:2
                    },
                    1000:{
                        items:3
                    }
                }
            })
        },

        // ------------- Sponsor V1 Slider ----------------
        team_slider_home_v2: function() {
            $('.home-v2-team-slider-active').owlCarousel({
                loop:true,
                margin:30,
                dots: true,
                nav:false,
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:true,
                responsive:{
                    0: {
                        items:1
                    },
                    600:{
                        items:2
                    },
                    1000:{
                        items:3
                    }
                }
            })
        },

        // ------------- Sponsor V1 Slider ----------------
        hero_slider_home_v2: function() {
            $('.home-v2-hero-slider-active').owlCarousel({
                loop:true,
                margin:30,
                dots: true,
                nav:false,
                autoplay:true,
                autoplayTimeout:5000,
                autoplayHoverPause:true,
                items:1
            })
        },



        //============== IMG to SVG ==================
        imgToSvg: function() {
            function jetix_svg() {
                jQuery('img').each(function() {
                    var jQueryimg = jQuery(this);
                    var imgClass = jQueryimg.attr('class');
                    var imgURL = jQueryimg.attr('src');
                    jQuery.get(imgURL, function(data) {
                        // Get the SVG tag, ignore the rest
                        var jQuerysvg = jQuery(data).find('svg');

                        // Add replaced image's classes to the new SVG
                        if (typeof imgClass !== 'undefined') {
                            jQuerysvg = jQuerysvg.attr('class', imgClass + ' replaced-svg');
                        }
                        jQuerysvg = jQuerysvg.removeAttr('xmlns:a');
                        // Replace image with new SVG
                        jQueryimg.replaceWith(jQuerysvg);

                    }, 'xml');

                });
            }
            $(document).each(function() {
                jetix_svg();
            });
            $('a[href=#]').click(function(e) {
                e.preventDefault();
            });
        },

        //================== StopPropagations elements =================
        stopPropagationElements: () => {
            $('.main-menu, .header-account ,.search-popup-wrapper ,.single-faq').click(function(e) {
                e.stopPropagation()
            })
        },


        // ================ Document click to hide elements ==================
        elementHide: () => {
            $(document).click(function() {
                $('.header-account ,.main-menu ,.plate').removeClass('active');
                $('.overlay').removeClass('overlay');
            })
        },


        // aos_aimation
        aos_aimation: function(){
            AOS.init({
                duration: 1000,
                once: true,
            });
        },



        init: function() {
            allfunction.imgToSvg()

            allfunction.menu_icon()
            allfunction.menu_toggle()
            allfunction.mega_menu()
            allfunction.user_toogle()
            allfunction.search_toogle()
            allfunction.hamburger_icon()
            allfunction.counter_up()
            allfunction.popup_gallery()
            allfunction.gallery_v1_isotop()
            allfunction.gallery_v2_isotop()
            allfunction.popup_video()
            allfunction.countdown_clock()
            allfunction.accordion()
            allfunction.nice_select()
            allfunction.landing_v1_gallery_isotop()
            allfunction.landing_v2_gallery_isotop()
            allfunction.aos_aimation()


            // Slider
            allfunction.partner_slider()
            allfunction.upcoming_event_slider()
            allfunction.meet_speaker_slider()
            allfunction.sponsor_slider_home_v1()
            allfunction.home_v2_meet_speaker_slider()
            allfunction.team_slider_home_v2()
            allfunction. hero_slider_home_v2()

            allfunction.stopPropagationElements()
            allfunction.elementHide()
        },
    }

    $(document).ready(function() {
        allfunction.init();
        window.addEventListener('load',function(){
            document.querySelector('body').classList.add("loaded")
        });
    });

})(jQuery);


anime({
    targets: '#ftr-dots path',
    scale: [
        {value: .1, easing: 'easeOutSine', duration: 500},
        {value: 1, easing: 'easeOutSine', duration: 700}
    ],
    delay: anime.stagger(200, {grid: [20, 50], from: 'top'}),
    direction: 'alternate',
    loop: true
});
// Prelax Efect
var image = document.getElementsByClassName('thumbnail');
new simpleParallax(image, {
	delay: .6,
	transition: 'cubic-bezier(0,0,0,1)'
});

var image = document.getElementsByClassName('left-right');
new simpleParallax(image, {
    delay: .6,
	orientation: 'right'
});
