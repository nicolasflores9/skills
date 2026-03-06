/**
 * Carousel de Testimonios
 * Módulo testimonios Joomla 5/6
 * Usa jQuery (disponible en WebAssetManager)
 */

(function($) {
  'use strict';

  class TestimonialCarousel {
    constructor(element, config) {
      this.$carousel = $(element);
      this.config = config || window.TestimonialConfig || {};
      this.currentIndex = 0;
      this.itemCount = this.$carousel.find('.testimonial-item').length;
      this.autoplayTimer = null;

      this.init();
    }

    init() {
      // Validar que hay items
      if (this.itemCount === 0) return;

      this.bindEvents();
      this.setupDots();

      // Iniciar autoplay si está habilitado
      if (this.config.autoplay) {
        this.startAutoplay();
      }

      // Accessibility
      this.$carousel.attr('role', 'region');
      this.$carousel.attr('aria-label', 'Carrusel de testimonios');
    }

    bindEvents() {
      const self = this;

      // Botones de navegación
      $('.testimonios-nav .prev-btn').on('click', function() {
        self.showSlide(self.currentIndex - 1);
      });

      $('.testimonios-nav .next-btn').on('click', function() {
        self.showSlide(self.currentIndex + 1);
      });

      // Dots
      $(document).on('click', '.dots-container .dot', function() {
        const index = $(this).data('slide');
        self.showSlide(index);
      });

      // Pause on hover
      this.$carousel.on('mouseenter', function() {
        self.stopAutoplay();
      }).on('mouseleave', function() {
        if (self.config.autoplay) {
          self.startAutoplay();
        }
      });

      // Keyboard navigation
      $(document).on('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
          self.showSlide(self.currentIndex - 1);
        } else if (e.key === 'ArrowRight') {
          self.showSlide(self.currentIndex + 1);
        }
      });
    }

    showSlide(index) {
      // Circular navigation
      if (index >= this.itemCount) {
        index = 0;
      } else if (index < 0) {
        index = this.itemCount - 1;
      }

      this.currentIndex = index;

      // Actualizar items visibles
      this.$carousel.find('.testimonial-item').removeClass('active');
      this.$carousel.find('.testimonial-item').eq(index).addClass('active');

      // Actualizar dots
      $('.dots-container .dot').removeClass('active');
      $('.dots-container .dot').eq(index).addClass('active');

      // Reset autoplay timer
      if (this.config.autoplay) {
        this.stopAutoplay();
        this.startAutoplay();
      }

      // Emit custom event
      this.$carousel.trigger('testimonial-changed', [index]);
    }

    setupDots() {
      const self = this;
      for (let i = 0; i < this.itemCount; i++) {
        if (i === 0) {
          $('.dots-container .dot').eq(i).addClass('active');
        }
      }
    }

    startAutoplay() {
      const self = this;
      this.autoplayTimer = setInterval(function() {
        self.showSlide(self.currentIndex + 1);
      }, this.config.speed || 5000);
    }

    stopAutoplay() {
      if (this.autoplayTimer) {
        clearInterval(this.autoplayTimer);
        this.autoplayTimer = null;
      }
    }

    destroy() {
      this.stopAutoplay();
      this.$carousel.off();
      $(document).off('click', '.dots-container .dot');
      $(document).off('keydown');
    }
  }

  // Inicializar cuando DOM está listo
  document.addEventListener('DOMContentLoaded', function() {
    $('.mod-testimonios').each(function() {
      new TestimonialCarousel(
        document.getElementById('testimonialsCarousel'),
        window.TestimonialConfig
      );
    });
  });

  // Exportar para uso global
  window.TestimonialCarousel = TestimonialCarousel;

})(jQuery);
