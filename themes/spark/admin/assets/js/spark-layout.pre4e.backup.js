/**
 * Spark ERP Design System — Layout JavaScript
 * Version: 1.2.0
 * 
 * Manages sidebar collapse, expandable submenus, mobile off-canvas drawer,
 * active submenu persistence, and layout state persistence without jQuery.
 * 
 * Breakpoints:
 * - Desktop: >= 992px
 * - Tablet & Mobile (Off-canvas Drawer): <= 991px
 */

(function () {
  'use strict';

  var STORAGE_KEY = 'spark_sidebar_collapsed';

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
   * Initialize Spark layout controllers on DOMContentLoaded
   */
  function initSparkLayout() {
    var sidebar = document.querySelector('.spark-sidebar');
    var app = document.querySelector('.spark-app');
    var backdrop = document.querySelector('.spark-backdrop');
    var toggles = document.querySelectorAll('.spark-header-toggle, [data-spark-toggle="sidebar"]');

    if (!sidebar) {
      return; // No Spark sidebar on this page
    }

    // 1. Restore collapsed state on desktop (>= 992px)
    var isMobile = window.innerWidth <= 991;
    if (!isMobile) {
      var savedCollapsed = getStorage(STORAGE_KEY);
      if (savedCollapsed === 'true') {
        sidebar.classList.add('is-collapsed');
        if (app) app.classList.add('is-sidebar-collapsed');
      }
    }

    // 2. Setup toggle buttons (Hamburger)
    toggles.forEach(function (toggle) {
      toggle.addEventListener('click', function (e) {
        e.preventDefault();
        var mobile = window.innerWidth <= 991;

        if (mobile) {
          // Mobile/Tablet: Toggle off-canvas drawer
          var isOpen = sidebar.classList.toggle('is-mobile-open');
          if (backdrop) backdrop.classList.toggle('is-active', isOpen);
          if (app) app.classList.toggle('is-mobile-open', isOpen);
          toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        } else {
          // Desktop (>= 992px): Toggle collapsed 72px width
          var isCollapsed = sidebar.classList.toggle('is-collapsed');
          if (app) app.classList.toggle('is-sidebar-collapsed', isCollapsed);
          setStorage(STORAGE_KEY, isCollapsed ? 'true' : 'false');
          toggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
        }
      });
    });

    // 3. Setup backdrop click to close mobile drawer
    if (backdrop) {
      backdrop.addEventListener('click', function () {
        sidebar.classList.remove('is-mobile-open');
        backdrop.classList.remove('is-active');
        if (app) app.classList.remove('is-mobile-open');
      });
    }

    // 4. Setup expandable submenu accordions
    var expandableItems = sidebar.querySelectorAll('.spark-sidebar-item-expandable, .spark-sidebar-item.has-submenu');
    expandableItems.forEach(function (item) {
      var link = item.querySelector('.spark-sidebar-link');
      if (link && link.getAttribute('href') === 'javascript:void(0);') {
        link.addEventListener('click', function (e) {
          e.preventDefault();

          // If sidebar is collapsed on desktop, expanding an accordion un-collapses sidebar
          if (sidebar.classList.contains('is-collapsed') && window.innerWidth >= 992) {
            sidebar.classList.remove('is-collapsed');
            if (app) app.classList.remove('is-sidebar-collapsed');
            setStorage(STORAGE_KEY, 'false');
          }

          var isCurrentlyExpanded = item.classList.contains('is-expanded');

          // Close sibling accordions in the same menu level
          if (!isCurrentlyExpanded && item.parentElement) {
            var siblings = item.parentElement.querySelectorAll(':scope > .spark-sidebar-item-expandable.is-expanded, :scope > .spark-sidebar-item.is-expanded');
            siblings.forEach(function (sib) {
              if (sib !== item) {
                sib.classList.remove('is-expanded');
                var sibLink = sib.querySelector('.spark-sidebar-link');
                if (sibLink) sibLink.setAttribute('aria-expanded', 'false');
              }
            });
          }

          if (!isCurrentlyExpanded) {
            item.classList.add('is-expanded');
            link.setAttribute('aria-expanded', 'true');
          } else {
            item.classList.remove('is-expanded');
            link.setAttribute('aria-expanded', 'false');
          }
        });
      }
    });

    // 5. Active Submenu Auto-Highlight & Module Auto-Expand on Route Change/Load
    var currentPath = window.location.pathname.replace(/\/+$/, '');
    var fullUrl = window.location.href.split('#')[0].replace(/\/+$/, '');
    var subLinks = sidebar.querySelectorAll('.spark-sidebar-submenu-link, .spark-sidebar-submenu a');
    var matchedLink = null;

    subLinks.forEach(function (link) {
      if (!link.href || link.href === '#' || link.href.indexOf('javascript:') === 0) return;
      var linkUrl = link.href.split('#')[0].replace(/\/+$/, '');
      if (linkUrl === fullUrl || linkUrl === window.location.origin + currentPath) {
        matchedLink = link;
      }
    });

    if (matchedLink) {
      matchedLink.classList.add('is-active');
      var parentLi = matchedLink.closest('li');
      if (parentLi) parentLi.classList.add('is-active');
      var parentExpandable = matchedLink.closest('.spark-sidebar-item-expandable');
      if (parentExpandable) {
        parentExpandable.classList.add('is-expanded', 'is-active');
        var parentLink = parentExpandable.querySelector('.spark-sidebar-link');
        if (parentLink) parentLink.setAttribute('aria-expanded', 'true');
      }
    }

    // 6. Handle window resize cleanup
    var resizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        if (window.innerWidth >= 992) {
          // Close mobile off-canvas if resized to desktop
          sidebar.classList.remove('is-mobile-open');
          if (backdrop) backdrop.classList.remove('is-active');
          if (app) app.classList.remove('is-mobile-open');
        }
      }, 150);
    });
  }

  // Self-initialize
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSparkLayout);
  } else {
    initSparkLayout();
  }

  // Export minimal API for external triggering if needed
  window.SparkLayout = {
    init: initSparkLayout,
    toggleSidebar: function () {
      var toggle = document.querySelector('.spark-header-toggle');
      if (toggle) toggle.click();
    }
  };
})();
