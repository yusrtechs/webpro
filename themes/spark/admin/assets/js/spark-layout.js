/**
 * Spark ERP Design System — Layout JavaScript
 * Version: 1.4.0
 * 
 * Manages 2-level sidebar accordions, mobile off-canvas drawer,
 * active submenu persistence, layout state persistence, and dark mode toggle.
 * 
 * Breakpoints:
 * - Desktop: >= 992px
 * - Tablet & Mobile (Off-canvas Drawer): <= 991px
 */

(function () {
  'use strict';

  var STORAGE_KEY = 'spark_sidebar_collapsed';
  var THEME_KEY = 'spark_theme';

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
   * Apply theme to <html> and update toggle icon.
   * Only operates when Spark UI is active (.spark-app exists).
   */
  function applyTheme(theme) {
    var html = document.documentElement;
    var icon = document.getElementById('sparkThemeIcon');

    if (theme === 'dark') {
      html.setAttribute('data-theme', 'dark');
      if (icon) {
        icon.className = 'fa fa-sun-o';
      }
    } else {
      html.removeAttribute('data-theme');
      if (icon) {
        icon.className = 'fa fa-moon-o';
      }
    }
  }

  /**
   * Initialize dark mode from persisted preference (runs early to prevent flash).
   */
  function initThemeEarly() {
    // Only apply if spark-app exists (i.e. Spark UI is enabled)
    if (!document.querySelector('.spark-app') && !document.querySelector('.spark-ui')) {
      return;
    }
    var saved = getStorage(THEME_KEY);
    if (saved === 'dark') {
      applyTheme('dark');
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

    // ── 0. Dark Mode Toggle ──────────────────────────────────────────────────
    var themeToggle = document.getElementById('sparkThemeToggle');
    if (themeToggle) {
      // Apply saved theme (icon update — attribute was applied early)
      var savedTheme = getStorage(THEME_KEY);
      if (savedTheme === 'dark') {
        applyTheme('dark');
      }

      themeToggle.addEventListener('click', function (e) {
        e.preventDefault();
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        var newTheme = isDark ? 'light' : 'dark';
        applyTheme(newTheme);
        setStorage(THEME_KEY, newTheme);
      });
    }

    // ── 1. Restore collapsed state on desktop (>= 992px) ────────────────────
    var isMobile = window.innerWidth <= 991;
    if (!isMobile) {
      var savedCollapsed = getStorage(STORAGE_KEY);
      if (savedCollapsed === 'true') {
        sidebar.classList.add('is-collapsed');
        if (app) app.classList.add('is-sidebar-collapsed');
      }
    }

    // ── 2. Ensure mobile drawer starts closed on fresh page load ─────────────
    if (isMobile) {
      sidebar.classList.remove('is-mobile-open');
      if (backdrop) backdrop.classList.remove('is-active');
      if (app) app.classList.remove('is-mobile-open');
    }

    // ── 3. Setup toggle buttons (Hamburger) ─────────────────────────────────
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

    // ── 4. Setup backdrop click to close mobile drawer ──────────────────────
    if (backdrop) {
      backdrop.addEventListener('click', function () {
        sidebar.classList.remove('is-mobile-open');
        backdrop.classList.remove('is-active');
        if (app) app.classList.remove('is-mobile-open');
      });
    }

    // ── 5. Setup Level 1 (Top-Level Module) Expandable Accordions ───────────
    var topLevelItems = sidebar.querySelectorAll('.spark-sidebar-menu > .spark-sidebar-item-expandable');
    topLevelItems.forEach(function (item) {
      // Select ONLY the direct child link of this module item
      var link = item.querySelector(':scope > .spark-sidebar-link');
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

          // Close sibling top-level modules
          if (!isCurrentlyExpanded && item.parentElement) {
            var siblings = item.parentElement.querySelectorAll(':scope > .spark-sidebar-item-expandable.is-expanded');
            siblings.forEach(function (sib) {
              if (sib !== item) {
                sib.classList.remove('is-expanded');
                var sibLink = sib.querySelector(':scope > .spark-sidebar-link');
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

    // ── 6. Setup Level 2 (Nested Submenu Group) Accordions ──────────────────
    var nestedItems = sidebar.querySelectorAll('.spark-sidebar-nested-item');
    nestedItems.forEach(function (nestedItem) {
      var toggle = nestedItem.querySelector(':scope > .spark-sidebar-nested-toggle');
      if (toggle) {
        toggle.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation(); // Do not trigger parent module listeners

          var isExpanded = nestedItem.classList.toggle('is-expanded');
          toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        });
      }
    });

    // ── 7. Active Submenu Auto-Highlight & Deep Auto-Expand ─────────────────
    var currentPath = window.location.pathname.replace(/\/+$/, '');
    var fullUrl = window.location.href.split('#')[0].replace(/\/+$/, '');
    var subLinks = sidebar.querySelectorAll('.spark-sidebar-submenu-link');
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
      var parentLi = matchedLink.closest('.spark-sidebar-submenu-item');
      if (parentLi) parentLi.classList.add('is-active');

      // Auto-expand Level 2 nested group if matched inside one
      var parentNested = matchedLink.closest('.spark-sidebar-nested-item');
      if (parentNested) {
        parentNested.classList.add('is-expanded', 'is-active');
        var nestedToggle = parentNested.querySelector(':scope > .spark-sidebar-nested-toggle');
        if (nestedToggle) nestedToggle.setAttribute('aria-expanded', 'true');
      }

      // Auto-expand Level 1 parent module
      var parentModule = matchedLink.closest('.spark-sidebar-item-expandable');
      if (parentModule) {
        parentModule.classList.add('is-expanded', 'is-active');
        var moduleLink = parentModule.querySelector(':scope > .spark-sidebar-link');
        if (moduleLink) moduleLink.setAttribute('aria-expanded', 'true');
      }
    }

    // ── 8. Handle window resize cleanup ─────────────────────────────────────
    var resizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        if (window.innerWidth >= 992) {
          sidebar.classList.remove('is-mobile-open');
          if (backdrop) backdrop.classList.remove('is-active');
          if (app) app.classList.remove('is-mobile-open');
        }
      }, 150);
    });
  }

  // ── 9. Global Modal Outside-Click & ESC Lock ───────────────────────────
  function initGlobalModalLock() {
    var $ = window.jQuery || window.$;
    if (!$) return;

    // 1. Override Bootstrap 3 Modal Defaults globally
    if ($.fn && $.fn.modal) {
      if ($.fn.modal.Constructor && $.fn.modal.Constructor.DEFAULTS) {
        $.fn.modal.Constructor.DEFAULTS.backdrop = 'static';
        $.fn.modal.Constructor.DEFAULTS.keyboard = false;
      }

      if (!$.fn.modal._sparkLocked) {
        var origModal = $.fn.modal;
        var lockedModal = function (option, _relatedTarget) {
          if (typeof option === 'object' && option !== null) {
            option.backdrop = 'static';
            option.keyboard = false;
          }
          return origModal.apply(this, arguments);
        };
        lockedModal.Constructor = origModal.Constructor;
        lockedModal.noConflict = origModal.noConflict;
        lockedModal._sparkLocked = true;
        $.fn.modal = lockedModal;
      }
    }

    // 2. Enforce static backdrop and keyboard=false on every modal before/when shown
    $(document).off('show.bs.modal.sparkLock').on('show.bs.modal.sparkLock', '.modal', function (e) {
      if (e && e.target && e.target !== this) return;
      var $modal = $(this);
      if ($modal.attr('id') === 'modal-loading') return;

      $modal.attr('data-backdrop', 'static');
      $modal.attr('data-keyboard', 'false');

      var bsModal = $modal.data('bs.modal');
      if (bsModal && bsModal.options) {
        bsModal.options.backdrop = 'static';
        bsModal.options.keyboard = false;
      }
    });

    // 3. Prevent ESC key from closing any active Bootstrap modal
    $(document).off('keydown.sparkModalLock').on('keydown.sparkModalLock', function (e) {
      if (e.which === 27 || e.keyCode === 27 || e.key === 'Escape') {
        var $visibleModal = $('.modal.in:visible, .modal:visible').not('#modal-loading');
        if ($visibleModal.length > 0) {
          e.preventDefault();
          e.stopPropagation();
          return false;
        }
      }
    });

    // 4. Mark existing modals in DOM
    $('.modal').not('#modal-loading').each(function () {
      var $m = $(this);
      $m.attr('data-backdrop', 'static');
      $m.attr('data-keyboard', 'false');
      var bsModal = $m.data('bs.modal');
      if (bsModal && bsModal.options) {
        bsModal.options.backdrop = 'static';
        bsModal.options.keyboard = false;
      }
    });
  }

  // ── 10. Global DataTables Actions Dropdown & Portal Positioning Engine ──
  var currentOpenDropdown = null;

  function isTableDropdown($parent, $toggle) {
    if (!$parent || !$parent.length) return false;
    return $parent.closest('table, .table-responsive, .dataTables_wrapper, .spark-table, .dataTables_scroll, .dataTables_scrollBody, .jambo_table').length > 0 ||
           ($toggle && $toggle.closest('table, .table-responsive, .dataTables_wrapper, .spark-table, .dataTables_scroll, .dataTables_scrollBody, .jambo_table').length > 0);
  }

  function getDropdownPortal() {
    var portal = document.getElementById('spark-dropdown-portal');
    if (!portal) {
      portal = document.createElement('div');
      portal.id = 'spark-dropdown-portal';
      portal.className = 'spark-dropdown-portal spark-ui';
      portal.setAttribute('aria-hidden', 'true');
      document.body.appendChild(portal);
    }
    return window.jQuery ? window.jQuery(portal) : $(portal);
  }

  function positionPortalDropdown($menu, $btn, $parent) {
    if (!$btn || !$btn.length || !$menu || !$menu.length) return;

    var btn = $btn[0];
    var rect = btn.getBoundingClientRect();

    // If toggle button is hidden or detached
    if (rect.width === 0 && rect.height === 0) {
      closeCurrentPortalDropdown();
      return;
    }

    var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    // Temporarily make visible off-screen inside portal to measure real dimensions
    $menu.css({
      display: 'block',
      visibility: 'hidden',
      position: 'fixed',
      top: '-9999px',
      left: '-9999px',
      maxHeight: 'none',
      overflowY: 'visible',
      margin: '0',
      zIndex: '1050'
    });

    var menuWidth = $menu.outerWidth() || 180;
    var menuHeight = $menu.outerHeight() || 220;

    var spaceBelow = viewportHeight - rect.bottom;
    var spaceAbove = rect.top;

    // Dynamic Direction: Dropup when space below is insufficient and space above is larger
    var isDropup = (spaceBelow < (menuHeight + 12)) && (spaceAbove > spaceBelow);

    // Smart vertical height clamping if screen is extremely constrained
    var maxAvailableHeight = isDropup ? (spaceAbove - 16) : (spaceBelow - 16);
    if (menuHeight > maxAvailableHeight && maxAvailableHeight < 280) {
      var clampedHeight = Math.max(140, Math.floor(maxAvailableHeight));
      $menu.css({
        maxHeight: clampedHeight + 'px',
        overflowY: 'auto'
      });
      menuHeight = $menu.outerHeight();
    } else {
      $menu.css({
        maxHeight: '',
        overflowY: ''
      });
    }

    // Calculate Top position
    var top;
    if (isDropup) {
      top = rect.top - menuHeight - 4;
    } else {
      top = rect.bottom + 4;
    }

    // Clamping vertical coordinate to viewport boundaries
    top = Math.max(8, Math.min(top, viewportHeight - menuHeight - 8));

    // Calculate Left position (RTL & LTR awareness)
    var isRtl = (document.documentElement.getAttribute('dir') === 'rtl') ||
                (document.body && document.body.classList.contains('rtl')) ||
                (window.jQuery && window.jQuery('html').attr('dir') === 'rtl');

    var isPullRight = $menu.hasClass('pull-right') ||
                      $menu.hasClass('dropdown-menu-right') ||
                      $parent.hasClass('pull-right');

    var isPullLeft = $menu.hasClass('pull-left') ||
                     $menu.hasClass('dropdown-menu-left') ||
                     $parent.hasClass('pull-left');

    var left;
    if (isRtl) {
      if (isPullLeft) {
        left = rect.left;
      } else {
        left = rect.right - menuWidth;
      }
    } else {
      if (isPullRight) {
        left = rect.right - menuWidth;
      } else {
        left = rect.left;
      }
    }

    // Clamping horizontal coordinate to viewport boundaries (prevents mobile horizontal clipping)
    var minLeft = 8;
    var maxLeft = viewportWidth - menuWidth - 8;
    left = Math.max(minLeft, Math.min(left, maxLeft));

    // Z-Index Hierarchy: 1065 if inside open modal, otherwise 1050
    var inModal = $parent.closest('.modal').length > 0;
    var zIndex = inModal ? 1065 : 1050;

    // Apply active positioning & classes
    $menu
      .addClass('spark-portal-dropdown')
      .toggleClass('spark-placement-top', isDropup)
      .toggleClass('spark-placement-bottom', !isDropup)
      .css({
        visibility: 'visible',
        display: 'block',
        position: 'fixed',
        top: Math.round(top) + 'px',
        left: Math.round(left) + 'px',
        zIndex: zIndex
      });

    $parent.toggleClass('dropup', isDropup);
  }

  function restorePortalDropdown($menu) {
    if (!$menu || !$menu.length) return;

    var $placeholder = $menu.data('spark-placeholder');
    var $parent = $menu.data('spark-parent');

    if ($placeholder && $placeholder.parent && $placeholder.parent().length) {
      $placeholder.replaceWith($menu);
    } else if ($parent && $parent.length) {
      $menu.appendTo($parent);
    }

    if ($placeholder && $placeholder.length) {
      $placeholder.remove();
    }

    $menu
      .removeData('spark-placeholder')
      .removeData('spark-parent')
      .removeData('spark-btn')
      .removeClass('spark-portal-dropdown spark-placement-top spark-placement-bottom')
      .css({
        position: '',
        top: '',
        left: '',
        zIndex: '',
        display: '',
        visibility: '',
        maxHeight: '',
        overflowY: '',
        margin: ''
      });

    if ($parent && $parent.length) {
      $parent.removeClass('dropup');
    }
  }

  function closeCurrentPortalDropdown() {
    if (currentOpenDropdown) {
      var curr = currentOpenDropdown;
      currentOpenDropdown = null;
      restorePortalDropdown(curr.$menu);
      if (curr.$parent && curr.$parent.hasClass('open')) {
        curr.$parent.removeClass('open');
      }
    }
  }

  function initGlobalDataTablesDropdowns() {
    var $ = window.jQuery || window.$;
    if (!$) return;

    // Idempotent initialization
    $(document).off('.sparkDataTableDropdown');
    $(window).off('.sparkDataTableDropdown');
    window.removeEventListener('scroll', handleGlobalScrollOrResize, true);

    // 1. Hook into Bootstrap 3 show.bs.dropdown for table dropdowns
    $(document).on('show.bs.dropdown.sparkDataTableDropdown', '.dropdown, .btn-group', function (e) {
      var $parent = $(this);
      var $btn = $parent.find('[data-toggle="dropdown"]').first();
      var $menu = $parent.find('.dropdown-menu').first();

      if (!isTableDropdown($parent, $btn)) {
        return; // Non-table dropdowns (nav, header) keep native Bootstrap behavior
      }

      if (!$menu.length || !$btn.length) return;

      // Close and restore previous dropdown if open
      if (currentOpenDropdown && currentOpenDropdown.$menu[0] !== $menu[0]) {
        closeCurrentPortalDropdown();
      }

      var $portal = getDropdownPortal();
      var $placeholder = $('<div class="spark-dropdown-placeholder" style="display:none;" aria-hidden="true"></div>');
      $menu.before($placeholder);

      $menu.data('spark-placeholder', $placeholder);
      $menu.data('spark-parent', $parent);
      $menu.data('spark-btn', $btn);

      // Move menu into central portal
      $menu.appendTo($portal);

      currentOpenDropdown = {
        $parent: $parent,
        $menu: $menu,
        $btn: $btn,
        $placeholder: $placeholder
      };

      positionPortalDropdown($menu, $btn, $parent);
    });

    // 2. Hook into Bootstrap 3 hide/hidden events
    $(document).on('hide.bs.dropdown.sparkDataTableDropdown hidden.bs.dropdown.sparkDataTableDropdown', '.dropdown, .btn-group', function (e) {
      var $parent = $(this);
      if (currentOpenDropdown && currentOpenDropdown.$parent[0] === $parent[0]) {
        var curr = currentOpenDropdown;
        currentOpenDropdown = null;
        restorePortalDropdown(curr.$menu);
      }
    });

    // 3. Handle clicks on action links in portal
    $(document).on('click.sparkDataTableDropdown', '#spark-dropdown-portal .dropdown-menu a', function (e) {
      setTimeout(function () {
        closeCurrentPortalDropdown();
      }, 50);
    });

    // 4. Reposition or auto-dismiss on window resize or scroll
    function handleGlobalScrollOrResize() {
      if (!currentOpenDropdown) return;
      window.requestAnimationFrame(function () {
        if (currentOpenDropdown) {
          var rect = currentOpenDropdown.$btn[0].getBoundingClientRect();
          var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
          if (rect.bottom < 0 || rect.top > viewportHeight || (rect.width === 0 && rect.height === 0)) {
            closeCurrentPortalDropdown();
          } else {
            positionPortalDropdown(currentOpenDropdown.$menu, currentOpenDropdown.$btn, currentOpenDropdown.$parent);
          }
        }
      });
    }

    $(window).on('resize.sparkDataTableDropdown', handleGlobalScrollOrResize);
    window.addEventListener('scroll', handleGlobalScrollOrResize, true);

    // 5. DataTables Lifecycle Events: Ensure clean restore before redraws
    $(document).on('draw.dt.sparkDataTableDropdown page.dt.sparkDataTableDropdown order.dt.sparkDataTableDropdown search.dt.sparkDataTableDropdown xhr.dt.sparkDataTableDropdown', 'table', function () {
      closeCurrentPortalDropdown();
    });

    // 6. Modals: Clean restore on modal show/hide
    $(document).on('show.bs.modal.sparkDataTableDropdown hide.bs.modal.sparkDataTableDropdown', function () {
      closeCurrentPortalDropdown();
    });
  }

  // ── Early theme application (before DOMContentLoaded to prevent flash) ────
  initThemeEarly();

  // Self-initialize
  function onReady() {
    initSparkLayout();
    initGlobalModalLock();
    initGlobalDataTablesDropdowns();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onReady);
  } else {
    onReady();
  }

  if (window.jQuery || window.$) {
    (window.jQuery || window.$)(function () {
      initGlobalModalLock();
      initGlobalDataTablesDropdowns();
    });
  }

  // Export minimal API for external triggering if needed
  window.SparkLayout = {
    init: initSparkLayout,
    initModalLock: initGlobalModalLock,
    initDataTablesDropdowns: initGlobalDataTablesDropdowns,
    closeDataTablesDropdowns: closeCurrentPortalDropdown,
    toggleSidebar: function () {
      var toggle = document.querySelector('.spark-header-toggle');
      if (toggle) toggle.click();
    },
    toggleTheme: function () {
      var toggle = document.getElementById('sparkThemeToggle');
      if (toggle) toggle.click();
    }
  };
})();


