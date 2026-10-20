/**
 * Spark ERP - Dashboard Controller
 * Handles ECharts initialization, responsive resizing, and tab switching.
 */

(function () {
  'use strict';

  function initSparkDashboard() {
    initTabs();
    loadEChartsAndRender();
  }

  /* --------------------------------------------------------------------------
     Tab Switching for Recent Transactions
     -------------------------------------------------------------------------- */
  function initTabs() {
    var tabBtns = document.querySelectorAll('.spark-tab-btn');
    if (!tabBtns.length) return;

    tabBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var targetId = this.getAttribute('data-target');
        var parentCard = this.closest('.spark-table-card');
        if (!parentCard) return;

        // Toggle active tab buttons
        parentCard.querySelectorAll('.spark-tab-btn').forEach(function (b) {
          b.classList.remove('is-active');
        });
        this.classList.add('is-active');

        // Toggle active tab contents
        parentCard.querySelectorAll('.spark-tab-content').forEach(function (c) {
          c.classList.remove('is-active');
        });
        var targetContent = parentCard.querySelector('#' + targetId);
        if (targetContent) {
          targetContent.classList.add('is-active');
        }
      });
    });
  }

  /* --------------------------------------------------------------------------
     ECharts Loader & Render
     -------------------------------------------------------------------------- */
  function loadEChartsAndRender() {
    if (typeof window.echarts !== 'undefined') {
      renderCharts();
      return;
    }

    var assetsUrl = window.SparkDashboardData && window.SparkDashboardData.assetsUrl 
      ? window.SparkDashboardData.assetsUrl 
      : 'themes/default/admin/assets/';
    
    var echartsScript = document.createElement('script');
    echartsScript.src = assetsUrl + 'template1/vendors/echarts/dist/echarts.min.js';
    echartsScript.onload = function () {
      renderCharts();
    };
    echartsScript.onerror = function () {
      console.warn('Spark Dashboard: Failed to load local ECharts library.');
    };
    document.head.appendChild(echartsScript);
  }

  function renderCharts() {
    if (typeof window.echarts === 'undefined' || !window.SparkDashboardData) return;

    var data = window.SparkDashboardData;
    var isRTL = document.documentElement.getAttribute('dir') === 'rtl';
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';

    var brandPrimary = getComputedStyle(document.documentElement).getPropertyValue('--spark-primary').trim() || '#6366f1';
    var textColor = isDark ? '#94a3b8' : '#64748b';
    var gridColor = isDark ? '#334155' : '#f1f5f9';

    // 1. Sales vs Purchases 12-Month Trend Chart
    var salesPurchasesEl = document.getElementById('sparkSalesPurchasesChart');
    if (salesPurchasesEl) {
      var chart1 = echarts.init(salesPurchasesEl);
      var option1 = {
        tooltip: {
          trigger: 'axis',
          axisPointer: { type: 'cross', label: { backgroundColor: brandPrimary } }
        },
        legend: {
          data: [data.lang.sales || 'Sales', data.lang.purchases || 'Purchases'],
          textStyle: { color: textColor },
          top: 0
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '3%',
          top: '15%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          boundaryGap: false,
          data: data.months || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          axisLine: { lineStyle: { color: gridColor } },
          axisLabel: { color: textColor }
        },
        yAxis: {
          type: 'value',
          axisLine: { lineStyle: { color: gridColor } },
          splitLine: { lineStyle: { color: gridColor } },
          axisLabel: { color: textColor }
        },
        series: [
          {
            name: data.lang.sales || 'Sales',
            type: 'line',
            smooth: true,
            data: data.monthlySales || [],
            itemStyle: { color: brandPrimary },
            areaStyle: {
              color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: brandPrimary + '40' },
                { offset: 1, color: brandPrimary + '05' }
              ])
            }
          },
          {
            name: data.lang.purchases || 'Purchases',
            type: 'line',
            smooth: true,
            data: data.monthlyPurchases || [],
            itemStyle: { color: '#f59e0b' },
            areaStyle: {
              color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: 'rgba(245, 158, 11, 0.2)' },
                { offset: 1, color: 'rgba(245, 158, 11, 0.01)' }
              ])
            }
          }
        ]
      };
      chart1.setOption(option1);
      observeResize(salesPurchasesEl, chart1);
    }

    // 2. Profit & Loss Donut Chart
    var profitEl = document.getElementById('sparkProfitChart');
    if (profitEl) {
      var chart2 = echarts.init(profitEl);
      var option2 = {
        tooltip: { trigger: 'item' },
        legend: {
          bottom: '0%',
          left: 'center',
          textStyle: { color: textColor }
        },
        series: [
          {
            name: data.lang.profit_and_loss || 'P&L',
            type: 'pie',
            radius: ['45%', '70%'],
            avoidLabelOverlap: false,
            itemStyle: {
              borderRadius: 6,
              borderColor: isDark ? '#1e293b' : '#ffffff',
              borderWidth: 2
            },
            label: { show: false, position: 'center' },
            emphasis: {
              label: { show: true, fontSize: '14', fontWeight: 'bold', color: textColor }
            },
            data: [
              { value: data.plRevenues || 0, name: data.lang.Revenues || 'Revenues', itemStyle: { color: '#10b981' } },
              { value: data.plExpenses || 0, name: data.lang.expenses || 'Expenses', itemStyle: { color: '#ef4444' } },
              { value: data.plProfit || 0, name: data.lang.profit || 'Net Profit', itemStyle: { color: '#2563eb' } }
            ]
          }
        ]
      };
      chart2.setOption(option2);
      observeResize(profitEl, chart2);
    }

    // 3. Aged Receivables (Customer Dues)
    var recvEl = document.getElementById('sparkReceivablesChart');
    if (recvEl) {
      var chart3 = echarts.init(recvEl);
      var option3 = {
        tooltip: {
          trigger: 'axis',
          axisPointer: { type: 'shadow' },
          formatter: function(params) {
            if (!params || !params.length) return '';
            var p = params[0];
            var daysLabel = data.lang.days || 'Days';
            var val = Number(p.value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return p.name + ' ' + daysLabel + '<br/>' + p.marker + ' ' + p.seriesName + ': <strong>' + val + '</strong>';
          }
        },
        grid: { left: '3%', right: '4%', bottom: '3%', top: '10%', containLabel: true },
        xAxis: {
          type: 'category',
          data: ['0-30', '31-60', '61-90', '90+'],
          axisLine: { lineStyle: { color: gridColor } },
          axisLabel: { color: textColor }
        },
        yAxis: {
          type: 'value',
          axisLine: { lineStyle: { color: gridColor } },
          splitLine: { lineStyle: { color: gridColor } },
          axisLabel: {
            color: textColor,
            formatter: function(value) {
              if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
              if (value >= 1000) return (value / 1000).toFixed(1) + 'K';
              return value;
            }
          }
        },
        series: [
          {
            name: data.lang.Customers_Dues || 'Receivables',
            type: 'bar',
            data: data.agedReceivables || [0, 0, 0, 0],
            itemStyle: { color: '#06b6d4', borderRadius: [4, 4, 0, 0] }
          }
        ]
      };
      chart3.setOption(option3);
      observeResize(recvEl, chart3);
    }

    // 4. Aged Payables (Supplier Dues)
    var payEl = document.getElementById('sparkPayablesChart');
    if (payEl) {
      var chart4 = echarts.init(payEl);
      var option4 = {
        tooltip: {
          trigger: 'axis',
          axisPointer: { type: 'shadow' },
          formatter: function(params) {
            if (!params || !params.length) return '';
            var p = params[0];
            var daysLabel = data.lang.days || 'Days';
            var val = Number(p.value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return p.name + ' ' + daysLabel + '<br/>' + p.marker + ' ' + p.seriesName + ': <strong>' + val + '</strong>';
          }
        },
        grid: { left: '3%', right: '4%', bottom: '3%', top: '10%', containLabel: true },
        xAxis: {
          type: 'category',
          data: ['0-30', '31-60', '61-90', '90+'],
          axisLine: { lineStyle: { color: gridColor } },
          axisLabel: { color: textColor }
        },
        yAxis: {
          type: 'value',
          axisLine: { lineStyle: { color: gridColor } },
          splitLine: { lineStyle: { color: gridColor } },
          axisLabel: {
            color: textColor,
            formatter: function(value) {
              if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
              if (value >= 1000) return (value / 1000).toFixed(1) + 'K';
              return value;
            }
          }
        },
        series: [
          {
            name: data.lang.Suppliers_Dues || 'Payables',
            type: 'bar',
            data: data.agedPayables || [0, 0, 0, 0],
            itemStyle: { color: '#f97316', borderRadius: [4, 4, 0, 0] }
          }
        ]
      };
      chart4.setOption(option4);
      observeResize(payEl, chart4);
    }
  }

  function observeResize(element, chart) {
    if (typeof ResizeObserver !== 'undefined') {
      var ro = new ResizeObserver(function () {
        chart.resize();
      });
      ro.observe(element);
    } else {
      window.addEventListener('resize', function () {
        chart.resize();
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSparkDashboard);
  } else {
    initSparkDashboard();
  }
})();
