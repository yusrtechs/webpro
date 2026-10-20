/**
 * Spark ERP Design System — Global Auto-Dismiss Success Alerts
 * Version: 1.0.0
 * 
 * Centralized alert manager for Spark ERP:
 * - Auto-dismisses success alerts (.alert.alert-success, .spark-login-alert.alert-success)
 * - Configurable display duration (Default: 4000ms) with smooth fade-out (500ms)
 * - UX Hover pause & resume (freezes timer while user reads/hovers)
 * - Manual close button handler with clean timer cancellation
 * - MutationObserver for dynamically injected AJAX alerts and modals
 * - Safe exclusion of non-success alerts (.alert-danger, .alert-warning, .alert-info, .alert_paid)
 * - Zero regression, full RTL/LTR and Dark Mode support
 */

(function (window, document, $) {
  'use strict';

  // Centralized Configuration Constants
  var DEFAULT_DURATION = 4000;      // Time in ms before alert starts to fade out (4s)
  var FADE_DURATION = 500;          // Time in ms for fade-out transition (0.5s)
  var MIN_RESUME_DURATION = 1200;   // Minimum time in ms given when resuming after hover

  var currentDuration = DEFAULT_DURATION;

  // Selector Patterns
  var SUCCESS_SELECTOR = '.alert.alert-success, .spark-login-alert.alert-success, .alert-success';
  var EXCLUDE_SELECTOR = '.alert-danger, .alert-warning, .alert-info, .alert_paid, [data-no-dismiss="true"], .no-auto-dismiss';
  var INITIALIZED_ATTR = 'data-spark-alert-initialized';

  var observer = null;
  var isInitialized = false;

  /**
   * Check if an element is a valid success alert eligible for auto-dismiss
   */
  function isEligibleSuccessAlert(el) {
    if (!el || el.nodeType !== 1) {
      return false;
    }

    try {
      // Must match success selector
      if (!el.matches || !el.matches(SUCCESS_SELECTOR)) {
        return false;
      }

      // Must NOT match excluded alerts (errors, warnings, info, paid status badges)
      if (el.matches(EXCLUDE_SELECTOR)) {
        return false;
      }

      // Parent/ancestor check for specific non-dismiss containers if any
      if (el.closest && (el.closest('.no-auto-dismiss-container') || el.closest('[data-no-auto-dismiss]'))) {
        return false;
      }

      return true;
    } catch (e) {
      return false;
    }
  }

  /**
   * Check if element is currently visible in DOM
   */
  function isElementVisible(el) {
    if (!el) {
      return false;
    }
    var doc = el.ownerDocument || document;
    if (doc.body && doc.body.contains && !doc.body.contains(el)) {
      return false;
    }
    if (el.style && (el.style.display === 'none' || el.style.visibility === 'hidden')) {
      return false;
    }
    if (window.getComputedStyle) {
      try {
        var style = window.getComputedStyle(el);
        if (style && (style.display === 'none' || style.visibility === 'hidden')) {
          return false;
        }
      } catch (e) {
        // Fallback to offset checks
      }
    }
    if (el.offsetParent === null && el.offsetWidth === 0 && el.offsetHeight === 0) {
      return false;
    }
    return true;
  }

  /**
   * Clear all active timers & listeners on an alert element
   */
  function cleanupAlertState(el) {
    if (!el || !el._sparkAlert) {
      return;
    }

    var state = el._sparkAlert;
    if (state.timerId) {
      clearTimeout(state.timerId);
      state.timerId = null;
    }

    if (state.handlers) {
      try {
        el.removeEventListener('mouseenter', state.handlers.onMouseEnter);
        el.removeEventListener('mouseleave', state.handlers.onMouseLeave);
      } catch (e) {
        // Ignore if element already detached
      }
    }

    el._sparkAlert = null;
  }

  /**
   * Perform smooth dismissal & DOM removal of an alert element
   */
  function dismissElement(el, instant) {
    if (!el || !el.parentNode) {
      return;
    }

    cleanupAlertState(el);

    if (instant) {
      if ($) {
        $(el).remove();
      } else {
        el.parentNode.removeChild(el);
      }
      return;
    }

    // Smooth transition: fade opacity to 0, then slide up and remove from DOM
    if ($) {
      var $el = $(el);
      $el.stop(true, false)
        .fadeTo(FADE_DURATION, 0, function () {
          $el.slideUp(250, function () {
            $el.remove();
          });
        });
    } else {
      // Vanilla fallback
      try {
        el.style.transition = 'opacity ' + FADE_DURATION + 'ms ease, max-height 250ms ease, margin 250ms ease, padding 250ms ease';
        el.style.opacity = '0';
        setTimeout(function () {
          if (el.parentNode) {
            el.style.maxHeight = '0px';
            el.style.paddingTop = '0px';
            el.style.paddingBottom = '0px';
            el.style.marginTop = '0px';
            el.style.marginBottom = '0px';
            el.style.overflow = 'hidden';
            setTimeout(function () {
              if (el.parentNode) {
                el.parentNode.removeChild(el);
              }
            }, 250);
          }
        }, FADE_DURATION);
      } catch (e) {
        if (el.parentNode) {
          el.parentNode.removeChild(el);
        }
      }
    }
  }

  /**
   * Initialize auto-dismiss countdown on a single success alert
   */
  function setupAlert(el) {
    if (!isEligibleSuccessAlert(el)) {
      return;
    }

    // Prevent duplicate initializations
    if (el.getAttribute(INITIALIZED_ATTR) === 'true' && el._sparkAlert) {
      return;
    }

    // If currently hidden (e.g. display: none), wait until it is made visible
    if (!isElementVisible(el)) {
      el.setAttribute(INITIALIZED_ATTR, 'pending');
      return;
    }

    el.setAttribute(INITIALIZED_ATTR, 'true');

    var state = {
      startTime: Date.now(),
      totalDuration: currentDuration,
      remainingTime: currentDuration,
      isPaused: false,
      isFading: false,
      timerId: null,
      handlers: null
    };

    // Callback when countdown completes to initiate dismissal
    function onTimeout() {
      if (state.isPaused || !el.parentNode) {
        return;
      }
      state.isFading = true;
      dismissElement(el, false);
    }

    // Start timer
    state.timerId = setTimeout(onTimeout, state.totalDuration);

    // Hover UX: Pause countdown when mouse enters
    function onMouseEnter() {
      if (!state.isPaused && el.parentNode) {
        if (state.timerId) {
          clearTimeout(state.timerId);
          state.timerId = null;
        }

        var elapsed = Date.now() - state.startTime;
        state.remainingTime = Math.max(0, state.totalDuration - elapsed);
        state.isPaused = true;

        // If it already began fading, stop fade and restore opacity
        if (state.isFading) {
          state.isFading = false;
          if ($) {
            $(el).stop(true, false).css({ opacity: 1, display: '' });
          } else {
            el.style.opacity = '1';
          }
        }
      }
    }

    // Hover UX: Resume countdown when mouse leaves
    function onMouseLeave() {
      if (state.isPaused && el.parentNode) {
        state.startTime = Date.now();
        // Ensure user gets at least MIN_RESUME_DURATION ms
        state.totalDuration = Math.max(state.remainingTime, MIN_RESUME_DURATION);
        state.isPaused = false;
        state.isFading = false;
        state.timerId = setTimeout(onTimeout, state.totalDuration);
      }
    }

    state.handlers = {
      onMouseEnter: onMouseEnter,
      onMouseLeave: onMouseLeave
    };

    el.addEventListener('mouseenter', onMouseEnter);
    el.addEventListener('mouseleave', onMouseLeave);

    // Handle manual close button click to safely stop timers
    var closeBtn = el.querySelector('.close, [data-dismiss="alert"]');
    if (closeBtn) {
      closeBtn.addEventListener('click', function (e) {
        cleanupAlertState(el);
      }, { once: true });
    }

    el._sparkAlert = state;
  }

  /**
   * Scan a container for all uninitialized success alerts and set up auto-dismiss
   */
  function scanAndInit(root) {
    if (!root) {
      root = document;
    }

    try {
      // If root is itself a success alert
      if (root !== document && isEligibleSuccessAlert(root)) {
        setupAlert(root);
      }

      if (root.querySelectorAll) {
        var alerts = root.querySelectorAll(SUCCESS_SELECTOR);
        for (var i = 0; i < alerts.length; i++) {
          setupAlert(alerts[i]);
        }
      }
    } catch (e) {
      // Safe fail
    }
  }

  /**
   * Initialize MutationObserver to detect dynamic alerts (AJAX, modals, dynamic append, etc.)
   */
  function initObserver() {
    if (observer || typeof window.MutationObserver === 'undefined') {
      return;
    }

    try {
      observer = new window.MutationObserver(function (mutations) {
        for (var i = 0; i < mutations.length; i++) {
          var mutation = mutations[i];

          // 1. Check added nodes
          if (mutation.addedNodes && mutation.addedNodes.length > 0) {
            for (var j = 0; j < mutation.addedNodes.length; j++) {
              var node = mutation.addedNodes[j];
              if (node.nodeType === 1) {
                scanAndInit(node);
              }
            }
          }

          // 2. Check attribute changes (e.g. style changed from display:none to display:block, or class change)
          if (mutation.type === 'attributes' && mutation.target && mutation.target.nodeType === 1) {
            var target = mutation.target;
            if (isEligibleSuccessAlert(target)) {
              if (target.getAttribute(INITIALIZED_ATTR) !== 'true' && isElementVisible(target)) {
                setupAlert(target);
              }
            }
          }
        }
      });

      if (document.body) {
        observer.observe(document.body, {
          childList: true,
          subtree: true,
          attributes: true,
          attributeFilter: ['style', 'class', 'hidden']
        });
      }
    } catch (e) {
      // Observer fallback
    }
  }

  /**
   * Global SparkAlerts API Object
   */
  var SparkAlerts = {
    version: '1.0.0',

    /**
     * Set the duration in milliseconds before success alerts start fading
     */
    setDuration: function (ms) {
      if (typeof ms === 'number' && ms > 0) {
        currentDuration = ms;
      }
      return this;
    },

    /**
     * Get current duration setting
     */
    getDuration: function () {
      return currentDuration;
    },

    /**
     * Dismiss a specific alert element smoothly or instantly
     */
    dismiss: function (alertElement, instant) {
      var el = (alertElement && alertElement.jquery) ? alertElement[0] : alertElement;
      if (el) {
        dismissElement(el, !!instant);
      }
      return this;
    },

    /**
     * Pause auto-dismiss timer on an alert element
     */
    pause: function (alertElement) {
      var el = (alertElement && alertElement.jquery) ? alertElement[0] : alertElement;
      if (el && el._sparkAlert && el._sparkAlert.handlers) {
        el._sparkAlert.handlers.onMouseEnter();
      }
      return this;
    },

    /**
     * Resume auto-dismiss timer on an alert element
     */
    resume: function (alertElement) {
      var el = (alertElement && alertElement.jquery) ? alertElement[0] : alertElement;
      if (el && el._sparkAlert && el._sparkAlert.handlers) {
        el._sparkAlert.handlers.onMouseLeave();
      }
      return this;
    },

    /**
     * Trigger auto-dismiss scanning on a specific container or whole document
     */
    autoDismissSuccess: function (container) {
      var root = container;
      if (root && root.jquery) {
        root = root[0];
      }
      scanAndInit(root || document);
      return this;
    },

    /**
     * Main initialization method
     */
    init: function () {
      if (isInitialized) {
        scanAndInit(document);
        return this;
      }

      isInitialized = true;
      scanAndInit(document);
      initObserver();

      // Hook into jQuery AJAX completion if jQuery is available as additional safety
      if ($ && $(document).ajaxComplete) {
        $(document).ajaxComplete(function () {
          setTimeout(function () {
            scanAndInit(document);
          }, 50);
        });
      }

      return this;
    }
  };

  // Expose to window
  window.SparkAlerts = SparkAlerts;

  // Auto-run when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      SparkAlerts.init();
    });
  } else {
    // DOM already loaded
    SparkAlerts.init();
  }

  // Also hook with jQuery ready if present
  if ($) {
    $(function () {
      SparkAlerts.init();
    });
  }

})(window, document, window.jQuery || window.$);
