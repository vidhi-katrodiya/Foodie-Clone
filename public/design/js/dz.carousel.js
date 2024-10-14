/* JavaScript Document */
jQuery(document).ready(function() {
    'use strict';

	/*  Team Carousel = owl.carousel.js */
	jQuery('.mobile-carousel').owlCarousel({
		loop:true,
		margin:30,
		nav:false,
		autoplaySpeed: 3000,
		navSpeed: 3000,
		paginationSpeed: 3000,
		slideSpeed: 3000,
		smartSpeed: 3000,
        autoplay: true,
		dots: true,
		navText: ['<i class="ti-arrow-left"></i>', '<i class="ti-arrow-right"></i>'],
		responsive:{
			0:{
				items:1
			},
			600:{
				items:2
			},			
			
			767:{
				items:3
			},
			1200:{
				items:4
			}
		}
	})
	
});
/* Document .ready END */