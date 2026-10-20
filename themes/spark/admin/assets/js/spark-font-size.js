/**
 * Spark ERP Design System — Font Size Accessibility Control
 * Version: 1.0.0
 *
 * Manages the A / AA / AAA font-size toggle:
 *   - Reads/writes `spark_font_size` from localStorage
 *   - Sets `data-font-size` attribute on <html>
 *   - Updates the control button states (aria-pressed, .is-active)
 *   - Default level is AA (Medium) when no preference exists
 *
 * This script runs in two phases:
 *   1. initFontSizeEarly() — runs immediately to prevent FOUC (before DOMContentLoaded)
 *   2. initFontSizeControl() — runs on DOMContentLoaded to bind button events
 */

(function () {
  'use strict';

  var FONT_SIZE_KEY = 'spark_font_size';
  var DEFAULT_LEVEL = 'AA';
  var VALID_LEVELS = ['A', 'AA', 'AAA'];

  /**
   * Safely read from localStorage
   */
  function getStorage(key) {
    try {
      return localStorage.getItem(key);
    } catch (e) {
      return null;
    }
  }

  /**
   * Safely write to localStorage
   */
  function setStorage(key, value) {
    try {
      localStorage.setItem(key, value);
    } catch (e) {
      // Storage unavailable or quota exceeded — fail silently
    }
  }

  /**
   * Validate a font-size level string.
   * Returns the level if valid, or DEFAULT_LEVEL otherwise.
   */
  function validateLevel(level) {
    if (level && VALID_LEVELS.indexOf(level) !== -1) {
      return level;
    }
    return DEFAULT_LEVEL;
  }

  /**
   * Apply the font-size level to <html> element.
   * Sets data-font-size attribute which is consumed by _font-size-control.css.
   */
  function applyFontSize(level) {
    var html = document.documentElement;
    html.setAttribute('data-font-size', level);
  }

  /**
   * Update the control buttons to reflect the active level.
   * Manages .is-active class and aria-pressed attribute.
   */
  function updateButtons(level) {
    var buttons = document.querySelectorAll('.spark-font-size-btn');
    for (var i = 0; i < buttons.length; i++) {
      var btn = buttons[i];
      var btnLevel = btn.getAttribute('data-level');
      var isActive = (btnLevel === level);
      
      if (isActive) {
        btn.classList.add('is-active');
        btn.setAttribute('aria-pressed', 'true');
      } else {
        btn.classList.remove('is-active');
        btn.setAttribute('aria-pressed', 'false');
      }
    }
  }

  /**
   * Phase 1: Apply saved font-size level immediately to prevent flash of wrong size.
   * Runs before DOMContentLoaded.
   */
  function initFontSizeEarly() {
    var saved = getStorage(FONT_SIZE_KEY);
    var level = validateLevel(saved);
    applyFontSize(level);

    // If no preference was saved, persist the default
    if (!saved || VALID_LEVELS.indexOf(saved) === -1) {
      setStorage(FONT_SIZE_KEY, DEFAULT_LEVEL);
    }
  }

  /**
   * Phase 2: Bind click events to the font-size control buttons.
   * Runs on DOMContentLoaded.
   */
  function initFontSizeControl() {
    // Only operate when Spark UI is active
    if (!document.querySelector('.spark-app') && !document.querySelector('.spark-ui')) {
      return;
    }

    var saved = getStorage(FONT_SIZE_KEY);
    var currentLevel = validateLevel(saved);

    // Ensure the attribute is applied (belt & suspenders)
    applyFontSize(currentLevel);

    // Update button states
    updateButtons(currentLevel);

    // Bind click handlers to each font-size button
    var buttons = document.querySelectorAll('.spark-font-size-btn');
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].addEventListener('click', function (e) {
        e.preventDefault();
        var newLevel = this.getAttribute('data-level');
        if (!newLevel || VALID_LEVELS.indexOf(newLevel) === -1) {
          return;
        }

        // Apply immediately — no page reload needed
        applyFontSize(newLevel);
        updateButtons(newLevel);
        setStorage(FONT_SIZE_KEY, newLevel);
      });
    }
  }

  // ── Phase 1: Run immediately ──────────────────────────────────────────────
  initFontSizeEarly();

  // ── Phase 2: Run on DOMContentLoaded ──────────────────────────────────────
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFontSizeControl);
  } else {
    // DOM already ready
    initFontSizeControl();
  }

})();
