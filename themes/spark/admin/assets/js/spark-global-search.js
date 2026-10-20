/**
 * Spark ERP Design System — Global Command Center & Search Engine
 * Version: 1.0.0
 * 
 * Provides instant keyboard-driven search (Ctrl+K), relevance ranking,
 * Arabic/English normalization, recent searches history, and seamless
 * navigation strictly derived from the server-permitted navigation menu.
 */

(function (window, document) {
  'use strict';

  var STORAGE_KEY_RECENT = 'spark_recent_searches';
  var MAX_RECENT_ITEMS = 5;

  // Bilingual metadata and synonyms dictionary for key Spark ERP routes.
  // Note: Entries are ONLY indexed if the user possesses server-side permission
  // to view the corresponding link in the navigation menu.
  var ROUTE_DICTIONARY = [
    // Accounting / Accounts
    {
      pattern: 'accounts/cash_flow',
      type: 'REPORT',
      title_ar: 'قائمة التدفقات النقدية',
      title_en: 'Cash Flow Statement',
      aliases: ['التدفقات النقدية', 'تدفقات نقدية', 'تدفق نقدي', 'cash flow', 'cashflow', 'cash flows', 'statement of cash flows', 'السيولة']
    },
    {
      pattern: 'accounts/incomestatement',
      type: 'REPORT',
      title_ar: 'قائمة الدخل',
      title_en: 'Income Statement',
      aliases: ['الارباح والخسائر', 'الدخل', 'income statement', 'profit loss', 'p&l', 'profit and loss']
    },
    {
      pattern: 'accounts/balancesheet',
      type: 'REPORT',
      title_ar: 'الميزانية العمومية',
      title_en: 'Balance Sheet',
      aliases: ['المركز المالي', 'قائمة المركز المالي', 'balance sheet', 'financial position']
    },
    {
      pattern: 'accounts/trialbalance',
      type: 'REPORT',
      title_ar: 'ميزان المراجعة',
      title_en: 'Trial Balance',
      aliases: ['ميزان مراجعة', 'trial balance']
    },
    {
      pattern: 'accounts/ledgers',
      type: 'REPORT',
      title_ar: 'دفتر الأستاذ',
      title_en: 'General Ledger',
      aliases: ['دفاتر الاستاذ', 'كشف الحسابات', 'ledgers', 'general ledger']
    },
    {
      pattern: 'accounts/accountstatement',
      type: 'REPORT',
      title_ar: 'كشف حساب',
      title_en: 'Account Statement',
      aliases: ['كشف حساب عميل', 'كشف حساب مورد', 'account statement']
    },
    {
      pattern: 'accounts/accounts_tree',
      type: 'PAGE',
      title_ar: 'شجرة الحسابات',
      title_en: 'Chart of Accounts',
      aliases: ['دليل الحسابات', 'الشجرة المحاسبية', 'chart of accounts', 'accounts tree']
    },
    {
      pattern: 'accounts/entries',
      type: 'PAGE',
      title_ar: 'قيود اليومية',
      title_en: 'Journal Entries',
      aliases: ['سندات القيد', 'سند قيد', 'قيد يومية', 'journal entries', 'entries']
    },
    {
      pattern: 'accounts/receipts',
      type: 'PAGE',
      title_ar: 'سندات الصرف',
      title_en: 'Payment Receipts',
      aliases: ['سند صرف', 'صرف نقدية', 'payment receipt', 'receipts']
    },
    {
      pattern: 'accounts/catch_receipts',
      type: 'PAGE',
      title_ar: 'سندات القبض',
      title_en: 'Receipt Vouchers',
      aliases: ['سند قبض', 'قبض نقدية', 'receipt voucher', 'catch receipts']
    },
    {
      pattern: 'accounts/cost_centers',
      type: 'PAGE',
      title_ar: 'مراكز التكلفة',
      title_en: 'Cost Centers',
      aliases: ['مركز تكلفة', 'cost centers', 'cost center']
    },
    {
      pattern: 'accounts/tax',
      type: 'REPORT',
      title_ar: 'تقرير الضريبة (المحاسبة)',
      title_en: 'Tax Report (Accounting)',
      aliases: ['اقرار ضريبي', 'القيمة المضافة', 'ضريبة القيمة المضافة', 'tax report', 'vat report']
    },
    {
      pattern: 'accounts/settings',
      type: 'SETTING',
      title_ar: 'إعدادات النظام المحاسبي',
      title_en: 'Accounting System Settings',
      aliases: ['اعدادات الحسابات', 'accounting settings']
    },

    // Sales
    {
      pattern: 'sales/add',
      type: 'PAGE',
      title_ar: 'إضافة فاتورة مبيع',
      title_en: 'Add Sale',
      aliases: ['فاتورة مبيعات', 'بيع جديد', 'اضافة فاتورة', 'add sale', 'new sale', 'invoice']
    },
    {
      pattern: 'sales',
      type: 'PAGE',
      title_ar: 'قائمة المبيعات',
      title_en: 'Sales List',
      aliases: ['فواتير المبيعات', 'المبيعات', 'sales', 'sales list', 'invoices']
    },
    {
      pattern: 'pos',
      type: 'PAGE',
      title_ar: 'نقطة البيع (POS)',
      title_en: 'Point of Sale (POS)',
      aliases: ['الكاشير', 'كاشير', 'نقطة بيع', 'pos', 'point of sale', 'cashier']
    },
    {
      pattern: 'sales/quotes_index',
      type: 'PAGE',
      title_ar: 'عروض الأسعار',
      title_en: 'Quotations',
      aliases: ['عرض سعر', 'عروض اسعار', 'quotes', 'quotations']
    },
    {
      pattern: 'customers',
      type: 'PAGE',
      title_ar: 'العملاء',
      title_en: 'Customers',
      aliases: ['قائمة العملاء', 'عميل جديد', 'customers', 'clients']
    },
    {
      pattern: 'sales/reports',
      type: 'REPORT',
      title_ar: 'تقارير المبيعات',
      title_en: 'Sales Reports',
      aliases: ['تقرير المبيعات', 'sales reports', 'sales report']
    },

    // Purchases
    {
      pattern: 'purchases/add',
      type: 'PAGE',
      title_ar: 'إضافة فاتورة شراء',
      title_en: 'Add Purchase',
      aliases: ['فاتورة شراء', 'شراء جديد', 'add purchase', 'new purchase']
    },
    {
      pattern: 'purchases',
      type: 'PAGE',
      title_ar: 'قائمة المشتريات',
      title_en: 'Purchases List',
      aliases: ['فواتير المشتريات', 'المشتريات', 'purchases', 'purchases list']
    },
    {
      pattern: 'purchases/expenses',
      type: 'PAGE',
      title_ar: 'المصروفات',
      title_en: 'Expenses List',
      aliases: ['مصروف', 'مصاريف', 'سند صرف مصروف', 'expenses', 'expense list']
    },
    {
      pattern: 'suppliers',
      type: 'PAGE',
      title_ar: 'الموردين',
      title_en: 'Suppliers',
      aliases: ['قائمة الموردين', 'مورد جديد', 'suppliers', 'vendors']
    },
    {
      pattern: 'purchases/reports',
      type: 'REPORT',
      title_ar: 'تقارير المشتريات',
      title_en: 'Purchases Reports',
      aliases: ['تقرير المشتريات', 'purchases reports']
    },

    // Products / Warehouses
    {
      pattern: 'products/add',
      type: 'PAGE',
      title_ar: 'إضافة منتج',
      title_en: 'Add Product',
      aliases: ['صنف جديد', 'اضافة صنف', 'منتج جديد', 'add product', 'new item']
    },
    {
      pattern: 'products',
      type: 'PAGE',
      title_ar: 'قائمة المنتجات',
      title_en: 'Products List',
      aliases: ['المنتجات', 'الاصناف', 'المخزون', 'المستودع', 'products', 'items', 'inventory']
    },
    {
      pattern: 'products/warehouses',
      type: 'PAGE',
      title_ar: 'المستودعات',
      title_en: 'Warehouses',
      aliases: ['المخازن', 'مستودع', 'warehouses', 'stocks']
    },
    {
      pattern: 'products/categories',
      type: 'PAGE',
      title_ar: 'فئات المنتجات',
      title_en: 'Product Categories',
      aliases: ['الاقسام', 'تصنيفات الاصناف', 'categories']
    },
    {
      pattern: 'products/reports',
      type: 'REPORT',
      title_ar: 'تقارير المستودعات',
      title_en: 'Warehouse Reports',
      aliases: ['تقرير المخزون', 'حركة صنف', 'products reports', 'stock reports']
    },

    // System Settings & Users
    {
      pattern: 'system_settings',
      type: 'SETTING',
      title_ar: 'إعدادات النظام',
      title_en: 'System Settings',
      aliases: ['الاعدادات العامة', 'ضبط النظام', 'system settings', 'configuration']
    },
    {
      pattern: 'users',
      type: 'PAGE',
      title_ar: 'المستخدمين',
      title_en: 'Users List',
      aliases: ['المستخدمين والصلاحيات', 'قائمة المستخدمين', 'users', 'staff']
    },
    {
      pattern: 'reports/tax',
      type: 'REPORT',
      title_ar: 'تقرير الضريبة',
      title_en: 'Tax Report',
      aliases: ['تقرير القيمة المضافة', 'tax report', 'vat report']
    },
    {
      pattern: 'reports/profit_loss',
      type: 'REPORT',
      title_ar: 'تقرير الأرباح والخسائر',
      title_en: 'Profit and Loss Report',
      aliases: ['الارباح والخسائر', 'profit and loss']
    }
  ];

  var SparkGlobalSearch = {
    initialized: false,
    isOpen: false,
    items: [],
    filteredItems: [],
    selectedIndex: 0,
    currentQuery: '',
    isArabic: document.documentElement.getAttribute('dir') === 'rtl' || document.documentElement.getAttribute('lang') === 'ar',

    // DOM Elements
    elBackdrop: null,
    elModal: null,
    elInput: null,
    elClear: null,
    elClose: null,
    elResults: null,
    elStats: null,
    elTrigger: null,

    /**
     * Idempotent Initialization
     */
    init: function () {
      if (this.initialized) {
        return;
      }

      this.cacheElements();
      if (!this.elBackdrop || !this.elInput) {
        return; // Necessary markup not present
      }

      this.isArabic = document.documentElement.getAttribute('dir') === 'rtl' || document.documentElement.getAttribute('lang') === 'ar';
      this.buildIndex();
      this.bindEvents();
      this.initialized = true;
    },

    /**
     * Cache DOM Elements
     */
    cacheElements: function () {
      this.elBackdrop = document.getElementById('sparkCommandPalette');
      this.elModal = this.elBackdrop ? this.elBackdrop.querySelector('.spark-search-modal') : null;
      this.elInput = document.getElementById('sparkGlobalSearchInput');
      this.elClear = document.getElementById('sparkSearchClear');
      this.elClose = document.getElementById('sparkSearchClose');
      this.elResults = document.getElementById('sparkSearchResults');
      this.elStats = document.getElementById('sparkSearchStats');
      this.elTrigger = document.getElementById('sparkGlobalSearchTrigger');
    },

    /**
     * Build the in-memory Search Registry strictly from server-rendered sidebar navigation
     */
    buildIndex: function () {
      var self = this;
      var sidebarNav = document.getElementById('sparkSidebarNav');
      var indexedMap = {};
      var items = [];

      if (!sidebarNav) {
        this.items = [];
        return;
      }

      // Query all permitted clickable menu links inside the sidebar
      var links = sidebarNav.querySelectorAll('a.spark-sidebar-submenu-link, a.spark-sidebar-link');

      links.forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href || href === '#' || href.indexOf('javascript:') === 0) {
          return;
        }

        // Clean link text (remove badges like "جديد" / "New")
        var clone = link.cloneNode(true);
        var badges = clone.querySelectorAll('.spark-badge-new, .badge, .label');
        badges.forEach(function (b) { b.remove(); });
        var title = (clone.textContent || '').trim();
        if (!title) {
          return;
        }

        // Determine Module (Top-Level Category)
        var moduleItem = link.closest('.spark-sidebar-item');
        var moduleText = '';
        var moduleIcon = 'fa fa-cube';

        if (moduleItem) {
          var moduleTitleEl = moduleItem.querySelector('.spark-sidebar-text');
          if (moduleTitleEl) {
            moduleText = (moduleTitleEl.textContent || '').trim();
          }
          var moduleIconEl = moduleItem.querySelector('.spark-sidebar-icon');
          if (moduleIconEl) {
            moduleIcon = moduleIconEl.className;
          }
        }

        // Determine Nested Category / Breadcrumb
        var nestedItem = link.closest('.spark-sidebar-nested-item');
        var nestedText = '';
        var itemIcon = moduleIcon;

        if (nestedItem) {
          var nestedTitleEl = nestedItem.querySelector('.spark-sidebar-nested-text');
          if (nestedTitleEl) {
            var nClone = nestedTitleEl.cloneNode(true);
            var nBadges = nClone.querySelectorAll('.spark-badge-new, .badge, .label');
            nBadges.forEach(function (b) { b.remove(); });
            nestedText = (nClone.textContent || '').trim();
          }
          var nestedIconEl = nestedItem.querySelector('.spark-sidebar-nested-icon');
          if (nestedIconEl) {
            itemIcon = nestedIconEl.className;
          }
        }

        // Build breadcrumb trail
        var breadcrumbs = [];
        if (moduleText) breadcrumbs.push(moduleText);
        if (nestedText && nestedText !== moduleText) breadcrumbs.push(nestedText);

        // Check against dictionary for aliases, dual-language titles & type classification
        var matchedDict = null;
        for (var i = 0; i < ROUTE_DICTIONARY.length; i++) {
          var d = ROUTE_DICTIONARY[i];
          if (href.indexOf(d.pattern) !== -1) {
            matchedDict = d;
            break;
          }
        }

        // Deduce item type
        var itemType = 'PAGE';
        var lowerHref = href.toLowerCase();
        var lowerTitle = title.toLowerCase();
        var lowerBreadcrumb = breadcrumbs.join(' ').toLowerCase();

        if (matchedDict && matchedDict.type) {
          itemType = matchedDict.type;
        } else if (lowerHref.indexOf('report') !== -1 || lowerTitle.indexOf('تقرير') !== -1 || lowerTitle.indexOf('report') !== -1 || lowerBreadcrumb.indexOf('تقارير') !== -1 || lowerBreadcrumb.indexOf('report') !== -1) {
          itemType = 'REPORT';
        } else if (lowerHref.indexOf('setting') !== -1 || lowerTitle.indexOf('إعداد') !== -1 || lowerTitle.indexOf('setting') !== -1 || lowerBreadcrumb.indexOf('إعداد') !== -1 || lowerBreadcrumb.indexOf('setting') !== -1) {
          itemType = 'SETTING';
        }

        var key = href.replace(/#.*$/, '');
        if (indexedMap[key]) {
          return; // Avoid duplicates
        }
        indexedMap[key] = true;

        var aliases = matchedDict ? (matchedDict.aliases || []) : [];
        var localizedTitle = matchedDict ? (self.isArabic ? (matchedDict.title_en || '') : (matchedDict.title_ar || '')) : '';

        items.push({
          title: title,
          localizedTitle: localizedTitle,
          url: href,
          icon: itemIcon,
          module: moduleText,
          breadcrumb: breadcrumbs.join(' / '),
          type: itemType,
          aliases: aliases
        });
      });

      this.items = items;
    },

    /**
     * Arabic Normalization (Tashkeel, Alef forms, Tatweel, whitespace)
     */
    normalizeArabic: function (str) {
      if (!str) return '';
      return String(str)
        .replace(/[\u064B-\u065F\u0670]/g, '') // Remove tashkeel / harakat
        .replace(/[إأآا]/g, 'ا')               // Normalize Alef variations to standard Alef
        .replace(/\u0640/g, '')                 // Remove tatweel / kashida
        .replace(/\s+/g, ' ')                  // Collapse spaces
        .trim()
        .toLowerCase();
    },

    /**
     * English Normalization (lowercase, spaces, punctuation)
     */
    normalizeEnglish: function (str) {
      if (!str) return '';
      return String(str)
        .toLowerCase()
        .replace(/[_\-\/]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    },

    /**
     * Combined Normalization
     */
    normalize: function (str) {
      if (!str) return '';
      var ar = this.normalizeArabic(str);
      return this.normalizeEnglish(ar);
    },

    /**
     * Search & Relevance Scoring Algorithm
     */
    search: function (query) {
      var self = this;
      var q = self.normalize(query);
      if (!q) {
        return [];
      }

      var scoredResults = [];

      for (var i = 0; i < self.items.length; i++) {
        var item = self.items[i];
        var titleNorm = self.normalize(item.title);
        var localizedTitleNorm = self.normalize(item.localizedTitle);
        var moduleNorm = self.normalize(item.module);
        var breadcrumbNorm = self.normalize(item.breadcrumb);
        var score = 0;

        // 1. Exact title match
        if (titleNorm === q || localizedTitleNorm === q) {
          score = 100;
        }
        // 2. Starts with title
        else if (titleNorm.indexOf(q) === 0 || localizedTitleNorm.indexOf(q) === 0) {
          score = 80;
        }
        // 3. Contains title substring
        else if (titleNorm.indexOf(q) !== -1 || localizedTitleNorm.indexOf(q) !== -1) {
          score = 60;
        }
        // 4. Matches aliases
        else if (item.aliases && item.aliases.length > 0) {
          for (var a = 0; a < item.aliases.length; a++) {
            var aliasNorm = self.normalize(item.aliases[a]);
            if (aliasNorm === q) {
              score = Math.max(score, 75);
              break;
            } else if (aliasNorm.indexOf(q) === 0) {
              score = Math.max(score, 55);
            } else if (aliasNorm.indexOf(q) !== -1) {
              score = Math.max(score, 45);
            }
          }
        }

        // 5. Breadcrumb / Module match
        if (score === 0) {
          if (moduleNorm === q || breadcrumbNorm === q) {
            score = 30;
          } else if (moduleNorm.indexOf(q) !== -1 || breadcrumbNorm.indexOf(q) !== -1) {
            score = 20;
          }
        }

        if (score > 0) {
          scoredResults.push({
            item: item,
            score: score
          });
        }
      }

      // Sort results by score descending
      scoredResults.sort(function (a, b) {
        return b.score - a.score;
      });

      return scoredResults.map(function (res) {
        return res.item;
      });
    },

    /**
     * Highlight matching terms safely
     */
    highlight: function (text, query) {
      if (!text || !query) return this.escapeHtml(text || '');
      var q = query.trim();
      if (!q) return this.escapeHtml(text);

      var escapedText = this.escapeHtml(text);
      var escapedQ = this.escapeRegex(q);

      try {
        var regex = new RegExp('(' + escapedQ + ')', 'gi');
        return escapedText.replace(regex, '<mark class="spark-search-highlight">$1</mark>');
      } catch (e) {
        return escapedText;
      }
    },

    escapeHtml: function (str) {
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    },

    escapeRegex: function (str) {
      return String(str).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    },

    /**
     * Render Search Results List
     */
    renderResults: function (results, isRecent) {
      var self = this;
      self.filteredItems = results || [];
      self.selectedIndex = 0;

      if (!self.elResults) return;

      if (results.length === 0) {
        var emptyTitle = self.isArabic ? 'لم يتم العثور على نتائج' : 'No results found';
        var emptyDesc = self.isArabic
          ? 'جرّب البحث بكلمات أخرى مثل "المبيعات"، "التدفقات النقدية"، أو "المنتجات".'
          : 'Try searching with other keywords like "Sales", "Cash Flow", or "Products".';

        self.elResults.innerHTML = [
          '<div class="spark-search-empty">',
          '  <div class="spark-search-empty-icon"><i class="fa fa-search-minus"></i></div>',
          '  <div class="spark-search-empty-title">' + emptyTitle + '</div>',
          '  <div class="spark-search-empty-desc">' + emptyDesc + '</div>',
          '</div>'
        ].join('');

        if (self.elStats) {
          self.elStats.textContent = self.isArabic ? '0 نتائج' : '0 results';
        }
        return;
      }

      var sectionHeader = '';
      if (isRecent) {
        var recentTitle = self.isArabic ? 'الصفحات الأخيرة' : 'Recent Searches';
        var clearText = self.isArabic ? 'مسح السجل' : 'Clear Recent';
        sectionHeader = [
          '<div class="spark-search-section-header">',
          '  <span>' + recentTitle + '</span>',
          '  <button type="button" class="spark-search-section-action" id="sparkClearRecentBtn">' + clearText + '</button>',
          '</div>'
        ].join('');
      } else {
        var resTitle = self.isArabic ? 'النتائج' : 'Results';
        sectionHeader = '<div class="spark-search-section-header"><span>' + resTitle + '</span></div>';
      }

      var listHtml = '<ul class="spark-search-list" role="listbox" id="sparkSearchResultsList">';

      results.forEach(function (item, idx) {
        var isSelected = idx === 0 ? ' is-selected' : '';
        var highlightedTitle = isRecent ? self.escapeHtml(item.title) : self.highlight(item.title, self.currentQuery);

        var badgeClass = 'badge-page';
        var badgeLabel = self.isArabic ? 'صفحة' : 'PAGE';
        if (item.type === 'REPORT') {
          badgeClass = 'badge-report';
          badgeLabel = self.isArabic ? 'تقرير' : 'REPORT';
        } else if (item.type === 'SETTING') {
          badgeClass = 'badge-setting';
          badgeLabel = self.isArabic ? 'إعداد' : 'SETTING';
        }

        var breadcrumbHtml = item.breadcrumb ? self.escapeHtml(item.breadcrumb) : '';

        listHtml += [
          '<li class="spark-search-item' + isSelected + '" data-index="' + idx + '" role="option" aria-selected="' + (idx === 0 ? 'true' : 'false') + '">',
          '  <div class="spark-search-item-start">',
          '    <div class="spark-search-item-icon"><i class="' + (item.icon || 'fa fa-file-text-o') + '"></i></div>',
          '    <div class="spark-search-item-content">',
          '      <span class="spark-search-item-title">' + highlightedTitle + '</span>',
          '      <div class="spark-search-item-meta">',
          '        <span class="spark-search-item-breadcrumb">' + breadcrumbHtml + '</span>',
          '      </div>',
          '    </div>',
          '  </div>',
          '  <div class="spark-search-item-end">',
          '    <span class="spark-search-badge ' + badgeClass + '">' + badgeLabel + '</span>',
          '    <span class="spark-search-item-enter"><i class="fa fa-level-down fa-rotate-90"></i></span>',
          '  </div>',
          '</li>'
        ].join('');
      });

      listHtml += '</ul>';
      self.elResults.innerHTML = sectionHeader + listHtml;

      if (self.elStats) {
        var countStr = self.isArabic ? (results.length + ' نتيجة') : (results.length + ' results');
        self.elStats.textContent = countStr;
      }

      // Bind Clear Recent button if visible
      var clearBtn = document.getElementById('sparkClearRecentBtn');
      if (clearBtn) {
        clearBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          self.clearRecent();
          self.loadDefaultView();
        });
      }

      // Bind click on items
      var itemEls = self.elResults.querySelectorAll('.spark-search-item');
      itemEls.forEach(function (el) {
        el.addEventListener('click', function () {
          var index = parseInt(this.getAttribute('data-index'), 10);
          self.execute(index);
        });
        el.addEventListener('mouseenter', function () {
          var index = parseInt(this.getAttribute('data-index'), 10);
          self.setActiveIndex(index);
        });
      });
    },

    /**
     * Load Recent Searches or Suggestions for initial empty state
     */
    loadDefaultView: function () {
      var recent = this.loadRecent();
      if (recent && recent.length > 0) {
        this.renderResults(recent, true);
      } else {
        // Render top suggestions from permitted items (up to 6 items)
        var suggestions = this.items.slice(0, 6);
        this.renderResults(suggestions, false);
      }
    },

    /**
     * Set Active Index visually
     */
    setActiveIndex: function (index) {
      if (!this.filteredItems || this.filteredItems.length === 0) return;
      if (index < 0) index = this.filteredItems.length - 1;
      if (index >= this.filteredItems.length) index = 0;

      this.selectedIndex = index;

      var items = this.elResults.querySelectorAll('.spark-search-item');
      items.forEach(function (el, idx) {
        if (idx === index) {
          el.classList.add('is-selected');
          el.setAttribute('aria-selected', 'true');
          el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        } else {
          el.classList.remove('is-selected');
          el.setAttribute('aria-selected', 'false');
        }
      });
    },

    selectNext: function () {
      this.setActiveIndex(this.selectedIndex + 1);
    },

    selectPrevious: function () {
      this.setActiveIndex(this.selectedIndex - 1);
    },

    /**
     * Execute selected result
     */
    execute: function (index) {
      if (typeof index !== 'number') {
        index = this.selectedIndex;
      }
      if (!this.filteredItems || !this.filteredItems[index]) {
        return;
      }

      var item = this.filteredItems[index];
      this.saveRecent(item);
      this.close();

      // Navigate to selected URL
      if (item.url) {
        window.location.href = item.url;
      }
    },

    /**
     * Recent Searches Manager (LocalStorage)
     */
    loadRecent: function () {
      try {
        var raw = localStorage.getItem(STORAGE_KEY_RECENT);
        if (!raw) return [];
        var list = JSON.parse(raw);
        if (!Array.isArray(list)) return [];

        // Validate recent items against active permitted items
        var self = this;
        var validList = [];
        list.forEach(function (rec) {
          if (!rec || !rec.url) return;
          var isPermitted = self.items.some(function (it) {
            return it.url === rec.url;
          });
          if (isPermitted) {
            validList.push(rec);
          }
        });
        return validList.slice(0, MAX_RECENT_ITEMS);
      } catch (e) {
        return [];
      }
    },

    saveRecent: function (item) {
      if (!item || !item.url) return;
      try {
        var recent = this.loadRecent();
        // Remove existing item with same URL to avoid duplicate
        recent = recent.filter(function (r) {
          return r.url !== item.url;
        });
        // Prepend current item
        recent.unshift({
          title: item.title,
          url: item.url,
          icon: item.icon,
          module: item.module,
          breadcrumb: item.breadcrumb,
          type: item.type
        });
        // Limit to max items
        recent = recent.slice(0, MAX_RECENT_ITEMS);
        localStorage.setItem(STORAGE_KEY_RECENT, JSON.stringify(recent));
      } catch (e) {
        // Ignore quota/private mode errors
      }
    },

    clearRecent: function () {
      try {
        localStorage.removeItem(STORAGE_KEY_RECENT);
      } catch (e) {}
    },

    /**
     * Open Command Palette Modal
     */
    open: function () {
      if (this.isOpen) return;
      this.isOpen = true;

      // Re-index on open if items empty
      if (this.items.length === 0) {
        this.buildIndex();
      }

      if (this.elBackdrop) {
        this.elBackdrop.classList.add('is-open');
        this.elBackdrop.setAttribute('aria-hidden', 'false');
      }

      if (this.elInput) {
        this.elInput.value = '';
        this.currentQuery = '';
        this.elInput.setAttribute('aria-expanded', 'true');
        if (this.elClear) {
          this.elClear.classList.remove('is-visible');
        }
        this.loadDefaultView();
        setTimeout(function () {
          SparkGlobalSearch.elInput.focus();
        }, 50);
      }

      document.body.style.overflow = 'hidden';
    },

    /**
     * Close Command Palette Modal
     */
    close: function () {
      if (!this.isOpen) return;
      this.isOpen = false;

      if (this.elBackdrop) {
        this.elBackdrop.classList.remove('is-open');
        this.elBackdrop.setAttribute('aria-hidden', 'true');
      }

      if (this.elInput) {
        this.elInput.setAttribute('aria-expanded', 'false');
        this.elInput.blur();
      }

      document.body.style.overflow = '';
    },

    toggle: function () {
      if (this.isOpen) {
        this.close();
      } else {
        this.open();
      }
    },

    /**
     * Event Listeners Binding
     */
    bindEvents: function () {
      var self = this;

      // 1. Header Trigger button
      if (self.elTrigger) {
        self.elTrigger.addEventListener('click', function (e) {
          e.preventDefault();
          self.toggle();
        });
      }

      // 2. Global Shortcut: Ctrl+K / Cmd+K
      document.addEventListener('keydown', function (e) {
        var isK = (e.key === 'k' || e.key === 'K' || e.keyCode === 75);
        var isMod = (e.ctrlKey || e.metaKey);

        if (isMod && isK) {
          e.preventDefault();
          self.toggle();
        } else if (e.key === 'Escape' || e.keyCode === 27) {
          if (self.isOpen) {
            e.preventDefault();
            self.close();
          }
        }
      });

      // 3. Backdrop click to close
      if (self.elBackdrop) {
        self.elBackdrop.addEventListener('click', function (e) {
          if (e.target === self.elBackdrop) {
            self.close();
          }
        });
      }

      // 4. Close Button click
      if (self.elClose) {
        self.elClose.addEventListener('click', function (e) {
          e.preventDefault();
          self.close();
        });
      }

      // 5. Clear Button click
      if (self.elClear) {
        self.elClear.addEventListener('click', function (e) {
          e.preventDefault();
          self.elInput.value = '';
          self.currentQuery = '';
          self.elClear.classList.remove('is-visible');
          self.loadDefaultView();
          self.elInput.focus();
        });
      }

      // 6. Search Input keystrokes & navigation
      if (self.elInput) {
        self.elInput.addEventListener('input', function () {
          var val = (self.elInput.value || '').trim();
          self.currentQuery = val;

          if (val.length > 0) {
            if (self.elClear) self.elClear.classList.add('is-visible');
            var results = self.search(val);
            self.renderResults(results, false);
          } else {
            if (self.elClear) self.elClear.classList.remove('is-visible');
            self.loadDefaultView();
          }
        });

        self.elInput.addEventListener('keydown', function (e) {
          if (e.key === 'ArrowDown' || e.keyCode === 40) {
            e.preventDefault();
            self.selectNext();
          } else if (e.key === 'ArrowUp' || e.keyCode === 38) {
            e.preventDefault();
            self.selectPrevious();
          } else if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            self.execute();
          }
        });
      }
    }
  };

  // Expose to window for global access
  window.SparkGlobalSearch = SparkGlobalSearch;

  // Auto-initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      SparkGlobalSearch.init();
    });
  } else {
    SparkGlobalSearch.init();
  }

})(window, document);
