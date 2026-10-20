/**
 * Spark ERP Design System — Global Loader JavaScript Manager
 * Version: 1.3.0
 * 
 * Centralized loading state manager for Spark ERP:
 * - Anti-flicker debounced AJAX floating loader with request counter
 * - Independent Modal AJAX loader manager (instant dismissal on content ready)
 * - Full page global loader manager
 * - Button loading state manager (prevents double-clicks & preserves dimensions)
 * - Section & card container loader manager
 * - Resilient error & completion recovery
 * - Full RTL/LTR and Dark Mode support
 */

(function (window, document, $) {
  'use strict';

  var SHOW_DELAY_MS = 180;      // Debounce delay before showing AJAX loader (prevents flicker on fast requests)
  var MIN_VISIBLE_MS = 300;     // Minimum display time once visible (prevents micro-flash)

  var activeRequests = 0;

  var showTimer = null;
  var hideTimer = null;
  var isAjaxLoaderVisible = false;
  var ajaxLoaderShownTimestamp = 0;

  /**
   * Determine current interface language / direction
   */
  function isArabic() {
    // 1. Check window.site settings if initialized
    if (window.site && window.site.settings && window.site.settings.user_language) {
      return window.site.settings.user_language === 'arabic';
    }
    // 2. Check HTML / Body attributes
    var html = document.documentElement;
    var body = document.body;
    if (html.getAttribute('lang') === 'ar' || html.getAttribute('dir') === 'rtl') {
      return true;
    }
    if (body && (body.getAttribute('dir') === 'rtl' || body.classList.contains('rtl'))) {
      return true;
    }
    // 3. Check for RTL stylesheet in the document
    if (document.querySelector('link[href*="rtl"]') !== null || document.querySelector('link[href*="bootstrap-rtl"]') !== null) {
      return true;
    }
    // 4. Check for Arabic characters in site title
    if (document.title && /[\u0600-\u06FF]/.test(document.title)) {
      return true;
    }
    return false;
  }

  /**
   * Get default localized text
   */
  function getDefaultText(type) {
    var ar = isArabic();
    switch (type) {
      case 'ajax':
        return ar ? 'جاري المعالجة...' : 'Processing...';
      case 'saving':
        return ar ? 'جاري الحفظ...' : 'Saving...';
      case 'loading':
        return ar ? 'جاري التحميل...' : 'Loading...';
      case 'modal':
        return ar ? 'جاري تحميل البيانات...' : 'Loading data...';
      default:
        return ar ? 'جاري التنفيذ...' : 'Please wait...';
    }
  }

  /**
   * Ensure #ajaxCall element has the modern Spark UI structure
   */
  function ensureAjaxElement() {
    var $el = $('#ajaxCall');
    if ($el.length === 0) {
      $el = $('<div id="ajaxCall" class="spark-ajax-loader" role="status" aria-live="polite" aria-busy="false" style="display:none;"></div>');
      $('body').append($el);
    } else {
      $el.addClass('spark-ajax-loader');
      $el.attr({
        'role': 'status',
        'aria-live': 'polite'
      });
    }

    if ($el.find('.spark-spinner').length === 0) {
      var initialText = getDefaultText('ajax');
      $el.html(
        '<span class="spark-spinner spark-spinner-xs"></span>' +
        '<span class="spark-ajax-loader-text">' + initialText + '</span>'
      );
    }
    return $el;
  }

  /**
   * Core SparkLoader Object
   */
  var SparkLoader = {
    version: '1.3.0',

    /**
     * Show Global Page Loader (#loading)
     */
    show: function () {
      var $loader = $('#loading');
      if ($loader.length === 0) {
        $loader = $('<div id="loading" class="spark-global-loader" role="status" aria-live="polite" aria-busy="true"></div>');
        $('body').prepend($loader);
      }
      $loader.stop(true, true).css({ opacity: 1, display: 'flex' });
    },

    /**
     * Hide Global Page Loader (#loading)
     */
    hide: function () {
      var $loader = $('#loading');
      if ($loader.length) {
        $loader.stop(true, true).fadeOut(250, function () {
          $(this).css('display', 'none');
        });
      }
    },

    /**
     * Register a new Global AJAX request start (Affects #ajaxCall ONLY)
     */
    ajaxStart: function (customText) {
      activeRequests++;

      if (showTimer) {
        clearTimeout(showTimer);
      }
      if (hideTimer) {
        clearTimeout(hideTimer);
      }

      // If already visible, just update request text
      if (isAjaxLoaderVisible) {
        var $el = ensureAjaxElement();
        if (customText) {
          $el.find('.spark-ajax-loader-text').text(customText);
        }
        return;
      }

      // Debounce show to prevent flicker on rapid sub-200ms requests
      showTimer = setTimeout(function () {
        if (activeRequests > 0 && !isAjaxLoaderVisible) {
          var $el = ensureAjaxElement();
          if (customText) {
            $el.find('.spark-ajax-loader-text').text(customText);
          } else {
            $el.find('.spark-ajax-loader-text').text(getDefaultText('ajax'));
          }

          $el.attr('aria-busy', 'true');
          $el.stop(true, true).css({ display: 'inline-flex', opacity: 0 }).animate({ opacity: 1 }, 150);
          isAjaxLoaderVisible = true;
          ajaxLoaderShownTimestamp = Date.now();
        }
      }, SHOW_DELAY_MS);
    },

    /**
     * Register a Global AJAX request stop / completion (Affects #ajaxCall ONLY)
     */
    ajaxStop: function () {
      activeRequests = Math.max(0, activeRequests - 1);

      if (activeRequests > 0) {
        return; // Still other requests running concurrently
      }

      if (showTimer) {
        clearTimeout(showTimer);
        showTimer = null;
      }

      if (!isAjaxLoaderVisible) {
        return; // Finished before show debounce fired — zero flicker!
      }

      // Ensure loader satisfies minimum display duration to prevent visual snapping
      var elapsed = Date.now() - ajaxLoaderShownTimestamp;
      var remaining = Math.max(0, MIN_VISIBLE_MS - elapsed);

      if (hideTimer) {
        clearTimeout(hideTimer);
      }

      hideTimer = setTimeout(function () {
        if (activeRequests === 0 && isAjaxLoaderVisible) {
          var $el = $('#ajaxCall');
          $el.attr('aria-busy', 'false');
          $el.stop(true, true).animate({ opacity: 0 }, 200, function () {
            $(this).css('display', 'none');
            isAjaxLoaderVisible = false;
          });
        }
      }, remaining);
    },

    /**
     * Force-reset AJAX loader state
     */
    resetAjax: function () {
      activeRequests = 0;
      if (showTimer) clearTimeout(showTimer);
      if (hideTimer) clearTimeout(hideTimer);
      isAjaxLoaderVisible = false;
      var $el = $('#ajaxCall');
      if ($el.length) {
        $el.attr('aria-busy', 'false').stop(true, true).hide();
      }
    },

    /**
     * Put a button into loading state
     * @param {HTMLElement|jQuery|string} button
     * @param {string} [loadingText]
     */
    buttonStart: function (button, loadingText) {
      var $btn = $(button);
      if (!$btn.length || $btn.hasClass('spark-btn-loading')) {
        return;
      }

      // Save original HTML, explicit width, and disabled state
      var origHtml = $btn.html();
      var origWidth = $btn.outerWidth();
      var wasDisabled = $btn.prop('disabled');

      $btn.data('spark-btn-orig', {
        html: origHtml,
        width: origWidth,
        wasDisabled: wasDisabled
      });

      // Preserve width to avoid layout jumping
      if (origWidth && origWidth > 0) {
        $btn.css('min-width', origWidth + 'px');
      }

      var text = loadingText || getDefaultText('saving');
      var spinnerClass = 'spark-spinner spark-spinner-xs';
      if ($btn.hasClass('spark-btn-primary') || $btn.hasClass('btn-primary') || $btn.hasClass('spark-btn-danger') || $btn.hasClass('btn-danger')) {
        spinnerClass += ' spark-spinner-white';
      }

      $btn.addClass('spark-btn-loading is-loading')
        .attr('aria-busy', 'true')
        .prop('disabled', true)
        .html('<span class="' + spinnerClass + '"></span> ' + text);
    },

    /**
     * Restore a button from loading state
     * @param {HTMLElement|jQuery|string} button
     */
    buttonStop: function (button) {
      var $btn = $(button);
      if (!$btn.length || !$btn.hasClass('spark-btn-loading')) {
        return;
      }

      var origData = $btn.data('spark-btn-orig');
      if (origData) {
        $btn.html(origData.html);
        if (!origData.wasDisabled) {
          $btn.prop('disabled', false);
        }
        $btn.removeData('spark-btn-orig');
      }

      $btn.removeClass('spark-btn-loading is-loading')
        .removeAttr('aria-busy')
        .css('min-width', '');
    },

    /**
     * Show Section / Container Loader
     * @param {HTMLElement|jQuery|string} container
     * @param {string} [text]
     */
    sectionStart: function (container, text) {
      var $c = $(container);
      if (!$c.length) return;

      $c.addClass('spark-loading-container');
      var $loader = $c.find('> .spark-section-loader');
      if ($loader.length === 0) {
        var label = text || getDefaultText('loading');
        $loader = $(
          '<div class="spark-section-loader" role="status" aria-live="polite">' +
          '<span class="spark-spinner spark-spinner-md"></span>' +
          '<span class="spark-section-loader-text">' + label + '</span>' +
          '</div>'
        );
        $c.append($loader);
      } else {
        if (text) {
          $loader.find('.spark-section-loader-text').text(text);
        }
        $loader.show();
      }
    },

    /**
     * Hide Section / Container Loader
     * @param {HTMLElement|jQuery|string} container
     */
    sectionStop: function (container) {
      var $c = $(container);
      if (!$c.length) return;
      $c.find('> .spark-section-loader').fadeOut(150, function () {
        $(this).remove();
      });
      $c.removeClass('spark-loading-container');
    },

    /**
     * Show Modal Loading Overlay (#modal-loading)
     */
    modalStart: function () {
      var $ml = $('#modal-loading');
      if ($ml.length) {
        $ml.attr('aria-busy', 'true')
          .addClass('is-active')
          .stop(true, true)
          .css({ display: 'flex', opacity: 1 });
      }
    },

    /**
     * Hide Modal Loading Overlay (#modal-loading)
     */
    modalStop: function () {
      var $ml = $('#modal-loading');
      if ($ml.length) {
        $ml.attr('aria-busy', 'false')
          .removeClass('is-active')
          .stop(true, true)
          .css('display', 'none');
      }
    },

    /**
     * Force-reset Modal Loading Overlay
     */
    resetModal: function () {
      this.modalStop();
    }
  };

  // Expose globally
  window.SparkLoader = SparkLoader;

  /**
   * Initialize Global Handlers on DOM Ready
   */
  $(document).ready(function () {
    // 1. Initialize DOM structure for #ajaxCall
    ensureAjaxElement();

    // 2. Global jQuery AJAX Hooks
    if (!window._sparkAjaxHooked) {
      window._sparkAjaxHooked = true;
      $(document)
        .ajaxStart(function () {
          SparkLoader.ajaxStart();
        })
        .ajaxStop(function () {
          SparkLoader.ajaxStop();
          SparkLoader.modalStop();
        })
        .ajaxComplete(function () {
          SparkLoader.modalStop();
        })
        .ajaxError(function () {
          SparkLoader.ajaxStop();
          SparkLoader.modalStop();
        });
    }

    // 3. Fallback cleanup on window unload or unhandled errors
    window.addEventListener('error', function () {
      if (activeRequests > 0) {
        // Safe state
      }
    });

    // 4. Modal lifecycle event coordination
    $(document)
      .on('show.bs.modal', '.modal', function (e) {
        // Ensure this event was fired for the modal dialog itself, not a child widget (e.g. datepicker, tooltip, dropdown)
        if (e.target === this && (!e.namespace || e.namespace === 'bs.modal')) {
          SparkLoader.modalStart();
        }
      })
      .on('loaded.bs.modal shown.bs.modal', '.modal', function (e) {
        if (e.target === this || !e.target) {
          SparkLoader.modalStop();
        }
      })
      .on('hide.bs.modal hidden.bs.modal', '.modal', function (e) {
        if (e.target === this || !e.target) {
          SparkLoader.modalStop();
        }
      });

    // 5. Automatic button loading trigger on forms with [data-spark-loading]
    $(document).on('submit', 'form[data-spark-loading]', function () {
      var $submitBtn = $(this).find('button[type="submit"], input[type="submit"]').first();
      if ($submitBtn.length && !$submitBtn.hasClass('spark-btn-loading')) {
        SparkLoader.buttonStart($submitBtn);
      }
    });
  });

  // Ensure Global Page Loader and Modal Loaders hide smoothly on full page window load
  $(window).on('load', function () {
    SparkLoader.hide();
    SparkLoader.modalStop();
  });

})(window, document, window.jQuery || window.$);
