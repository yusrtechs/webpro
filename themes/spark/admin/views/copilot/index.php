<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ==================================================================
 * المحاسب الذكي — Smart Accountant · Full-3D Holo-Deck
 * The whole UI lives INSIDE a Three.js WebGL scene: the avatar, the
 * KPI holo-cards and the answer panels share one 3D world (WebGL +
 * CSS3D fused renderers, one camera). Drag orbits everything.
 * RTL/LTR aware, responsive, no external CDN (all libs vendored).
 * UI-first build: CopilotBrain.ask() is the single hook where the
 * real AI endpoint will be wired next (see TODO inside).
 * ================================================================== */

$is_ar = ($Settings->user_language == 'arabic');
$dir   = $is_ar ? 'rtl' : 'ltr';
$flip  = $is_ar ? -1 : 1;
$curr  = isset($Settings->default_currency) && $Settings->default_currency ? $Settings->default_currency : 'SR';

$salesTotal   = (float) ($allSales->grand_total ?? 0);
$salesPaid    = (float) ($allSales->paid ?? 0);
$salesDue     = (float) ($allSales->due ?? 0);
$salesCount   = (int)   ($allSales->total ?? 0);
$purchTotal   = (float) ($allPurchases->grand_total ?? 0);
$purchPaid    = (float) ($allPurchases->paid ?? 0);
$purchDue     = (float) ($allPurchases->due ?? 0);
$purchCount   = (int)   ($allPurchases->total ?? 0);
$taxSales     = (float) $salesTax;
$taxPurchases = (float) $purchasesTax;

$mSales = array_fill(1, 12, 0.0); $mSalesTax = array_fill(1, 12, 0.0);
foreach ((array) $monthlySales as $r) { $m = (int) $r->date; if ($m >= 1 && $m <= 12) { $mSales[$m] = (float) $r->total; $mSalesTax[$m] = (float) $r->tax1; } }
$mPurch = array_fill(1, 12, 0.0); $mPurchTax = array_fill(1, 12, 0.0);
foreach ((array) $monthlyPurchases as $r) { $m = (int) $r->date; if ($m >= 1 && $m <= 12) { $mPurch[$m] = (float) $r->total; $mPurchTax[$m] = (float) $r->tax1; } }

$t = $is_ar ? [
    'title'       => 'المحاسب الذكي',
    'subtitle'    => 'عالمك المالي ثلاثي الأبعاد',
    'online'      => 'متصل • النظام جاهز',
    'core'        => 'النواة الذكية v1.0',
    'loading'     => 'جارٍ بناء العالم ثلاثي الأبعاد…',
    'drag_hint'   => 'اسحب لتدوير العالم',
    'placeholder' => 'اسأل محاسبك… مثال: مبيعات هذا الشهر',
    'sales'       => 'المبيعات',
    'purchases'   => 'المشتريات',
    'tax'         => 'الضريبة',
    'received'    => 'المستلمة',
    'due'         => 'المتأخرة',
    'invoices'    => 'فاتورة',
    'vat_out'     => 'ضريبة المبيعات',
    'vat_in'      => 'ضريبة المشتريات',
    'vat_net'     => 'صافي الضريبة المستحقة',
    'open_report' => 'فتح التقرير الكامل',
    'from'        => 'من',
    'to'          => 'إلى',
    'chip1'       => 'مبيعات هذا الشهر',
    'chip2'       => 'مشتريات هذا العام',
    'chip3'       => 'ضريبة الربع الثاني',
    'chip4'       => 'قارن المبيعات والمشتريات',
    'greet_t'     => 'أهلاً بك 👋',
    'greet'       => 'أنا محاسبك الذكي. اطلب تقارير المبيعات أو المشتريات أو الضرائب لأي فترة — اكتب طلبك أو المس إحدى البطاقات العائمة حولي.',
    'unknown_t'   => 'لم أفهم الطلب بعد',
    'unknown'     => 'ما زلت أتعلّم! جرّب: «مبيعات هذا الشهر»، «مشتريات 2025»، «ضريبة الربع الأول»، أو «قارن المبيعات والمشتريات».',
    'sales_t'     => 'تقرير المبيعات',
    'purch_t'     => 'تقرير المشتريات',
    'tax_t'       => 'تقرير الضريبة',
    'cmp_t'       => 'مقارنة المبيعات والمشتريات',
    'total_is'    => 'الإجمالي خلال الفترة المحددة بلغ',
    'all_time'    => 'كامل الفترات',
    'range_note'  => 'الأرقام الشهرية متاحة لسنة',
    'listen'      => 'قراءة صوتية',
    'voice_on'    => 'الصوت مفعّل',
    'voice_off'   => 'الصوت متوقف',
    'thinking'    => 'يفكر…',
    'net_flow'    => 'صافي التدفق',
    'no_webgl'    => 'متصفحك لا يدعم WebGL — سيتم عرض نسخة مبسطة.',
    'best_month'  => 'أفضل شهر',
    'best_in'     => 'أعلى أداء سُجّل في',
    'vs_prev'     => 'عن الفترة السابقة',
    'margin'      => 'نسبة الصافي',
    'returns'     => 'المرتجعات',
    'chip5'       => 'فواتير هذا الشهر',
    'more'        => 'فاتورة أخرى',
    'inv_t'       => 'فواتير المبيعات',
    'inv_offline' => 'تعذّر جلب الفواتير الآن. تحقّق من الاتصال ثم أعد المحاولة، أو افتح قائمة الفواتير كاملة.',
] : [
    'title'       => 'Smart Accountant',
    'subtitle'    => 'Your 3D financial world',
    'online'      => 'Online • System ready',
    'core'        => 'AI Core v1.0',
    'loading'     => 'Building your 3D world…',
    'drag_hint'   => 'Drag to orbit the world',
    'placeholder' => 'Ask your accountant… e.g. sales this month',
    'sales'       => 'Sales',
    'purchases'   => 'Purchases',
    'tax'         => 'Tax',
    'received'    => 'Received',
    'due'         => 'Due',
    'invoices'    => 'invoices',
    'vat_out'     => 'Output VAT (sales)',
    'vat_in'      => 'Input VAT (purchases)',
    'vat_net'     => 'Net VAT payable',
    'open_report' => 'Open full report',
    'from'        => 'From',
    'to'          => 'To',
    'chip1'       => 'Sales this month',
    'chip2'       => 'Purchases this year',
    'chip3'       => 'VAT for Q2',
    'chip4'       => 'Compare sales & purchases',
    'greet_t'     => 'Welcome 👋',
    'greet'       => "I'm your smart accountant. Ask for sales, purchases or tax reports over any period — type a request or touch one of the cards floating around me.",
    'unknown_t'   => "I didn't catch that yet",
    'unknown'     => 'Still learning! Try: “sales this month”, “purchases 2025”, “VAT for Q1”, or “compare sales & purchases”.',
    'sales_t'     => 'Sales Report',
    'purch_t'     => 'Purchases Report',
    'tax_t'       => 'Tax Report',
    'cmp_t'       => 'Sales vs Purchases',
    'total_is'    => 'Total for the selected period is',
    'all_time'    => 'All time',
    'range_note'  => 'Monthly figures available for',
    'listen'      => 'Read aloud',
    'voice_on'    => 'Voice on',
    'voice_off'   => 'Voice off',
    'thinking'    => 'Thinking…',
    'net_flow'    => 'Net flow',
    'no_webgl'    => 'Your browser lacks WebGL — showing simplified view.',
    'best_month'  => 'Best month',
    'best_in'     => 'Best performance was in',
    'vs_prev'     => 'vs previous period',
    'margin'      => 'Net margin',
    'returns'     => 'Returns',
    'chip5'       => 'Invoices this month',
    'more'        => 'more',
    'inv_t'       => 'Sales Invoices',
    'inv_offline' => "I couldn't fetch the invoices just now. Check your connection and try again, or open the full invoice list.",
];
?>

<?php if ($this->input->get('modal')): ?>
    <link href="<?= $assets ?>styles/helpers/font-awesome.min.css" rel="stylesheet"/>
<?php endif; ?>
<style>
/* ===================== Holo-Deck (full 3D) ===================== */
.cp-wrap { padding: 10px 14px 18px; }
.cp-wrap, .cp-wrap * { box-sizing: border-box; }
.cp-deck {
    --cp-accent: #818cf8;
    --cp-accent2: #a78bfa;
    --cp-gold: #fbbf24;
    --cp-ink: #eef2ff;
    --cp-dim: #a5abd4;
    position: relative;
    direction: <?= $dir ?>;
    height: calc(100vh - 185px); /* fallback; JS fits it exactly to the viewport */
    min-height: 520px;
    border-radius: 26px;
    overflow: hidden;
    background: radial-gradient(120% 90% at 50% -10%, #2c2660 0%, #181341 45%, #0d0a24 100%);
    box-shadow: 0 0 0 1px rgba(129,140,248,.22), 0 0 60px rgba(129,140,248,.10) inset, 0 24px 60px rgba(11,8,30,.45);
    color: var(--cp-ink);
    font-family: 'Cairo', 'Cairo-Bold', 'Lato', -apple-system, 'Segoe UI', sans-serif;
    touch-action: pan-y;
}
.cp-aurora i { position: absolute; border-radius: 50%; filter: blur(90px); pointer-events: none; }
.cp-orb1 { width: 460px; height: 460px; background: rgba(129,140,248,.25); top: -150px; inset-inline-start: -110px; animation: cpFloat 12s ease-in-out infinite alternate; }
.cp-orb2 { width: 520px; height: 520px; background: rgba(167,139,250,.20); bottom: -220px; inset-inline-end: -140px; animation: cpFloat 14s -6s ease-in-out infinite alternate; }
.cp-orb3 { width: 280px; height: 280px; background: rgba(251,191,36,.09); top: 40%; inset-inline-start: 46%; animation: cpFloat 16s -3s ease-in-out infinite alternate; }
@keyframes cpFloat { to { transform: translate3d(46px, 34px, 0) scale(1.08); } }

/* --- fused renderer layers: one 3D world --- */
.cp-gl, .cp-css3d { position: absolute; inset: 0; }
.cp-gl { z-index: 1; cursor: grab; }
.cp-gl.grabbing { cursor: grabbing; }
/* CSS3DRenderer assumes LTR geometry: keep the 3D layer LTR always;
   each holder re-applies the real text direction for its content */
.cp-css3d { z-index: 2; pointer-events: none; overflow: hidden; direction: ltr; }
.cp-css3d .cp3-holder { pointer-events: auto; left: 0; top: 0; }

/* ---------- HUD ---------- */
.cp-hud {
    position: absolute; z-index: 30; inset-inline: 0; top: 0;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 16px 22px; pointer-events: none;
    background: linear-gradient(to bottom, rgba(12,9,32,.78), transparent);
    transform: perspective(900px) rotateX(calc(var(--cp-my, 0) * -1.5deg)) rotateY(calc(var(--cp-mx, 0) * 2deg));
}
.cp-hud > * { pointer-events: auto; }
.cp-hud-id { display: flex; align-items: center; gap: 12px; min-width: 0; }
.cp-hud-logo {
    width: 42px; height: 42px; flex: 0 0 42px; border-radius: 13px;
    display: grid; place-items: center; font-size: 19px; color: #1a1442;
    background: linear-gradient(135deg, var(--cp-accent), var(--cp-accent2));
    box-shadow: 0 0 18px rgba(129,140,248,.55);
    animation: cpLogoPulse 3.2s ease-in-out infinite;
}
@keyframes cpLogoPulse { 50% { box-shadow: 0 0 30px rgba(129,140,248,.85); } }
.cp-hud-id h1 { margin: 0; font-size: 19px; font-weight: 800; color: var(--cp-ink); font-family: 'Cairo-Bold','Cairo',sans-serif; line-height: 1.2; white-space: nowrap; }
.cp-hud-id p { margin: 0; font-size: 11.5px; color: var(--cp-dim); white-space: nowrap; }
.cp-hud-status { display: flex; align-items: center; gap: 14px; font-size: 12px; color: var(--cp-dim); }
.cp-dot { width: 8px; height: 8px; border-radius: 50%; background: #34d399; box-shadow: 0 0 10px #34d399; display: inline-block; animation: cpTwinkle 2.4s infinite; }
@keyframes cpTwinkle { 50% { opacity: .35; } }
.cp-hud-core { padding: 5px 12px; border: 1px solid rgba(129,140,248,.35); border-radius: 999px; color: var(--cp-accent); letter-spacing: .4px; white-space: nowrap; }
.cp-voice {
    background: none; border: 1px solid rgba(165,171,212,.35); color: var(--cp-dim);
    border-radius: 999px; width: 34px; height: 34px; cursor: pointer; display: grid; place-items: center; font-size: 15px;
    transition: all .25s;
}
.cp-voice.on { color: #1a1442; background: var(--cp-accent); border-color: var(--cp-accent); box-shadow: 0 0 14px rgba(129,140,248,.6); }
.cp-hint {
    position: absolute; z-index: 28; top: 74px; inset-inline-start: 50%; transform: translateX(calc(<?= $flip ?> * -50%));
    font-size: 11.5px; color: var(--cp-dim); letter-spacing: .4px; pointer-events: none;
    padding: 5px 14px; border-radius: 999px; background: rgba(12,9,32,.5); border: 1px solid rgba(165,171,212,.2);
    animation: cpHint 6s forwards;
}
@keyframes cpHint { 0%,70% { opacity: 1; } 100% { opacity: 0; visibility: hidden; } }

/* ---------- 3D holo-cards (rendered inside the scene by CSS3D) ---------- */
.cp3-card {
    width: 300px; padding: 16px 18px; border-radius: 18px; cursor: pointer;
    /* frosted glass: the 3D world shows through */
    background: linear-gradient(150deg, rgba(180,175,255,.13), rgba(110,95,200,.05));
    backdrop-filter: blur(16px) saturate(160%);
    -webkit-backdrop-filter: blur(16px) saturate(160%);
    border: 1px solid rgba(196,190,255,.30);
    box-shadow: 0 14px 34px rgba(10,7,26,.35),
                inset 0 1px 0 rgba(255,255,255,.25),
                inset 0 0 26px rgba(129,140,248,.07);
    transition: box-shadow .35s, border-color .35s, opacity .45s, filter .45s, background .35s;
    position: relative; overflow: hidden;
}
.cp3-card::before {
    content: ''; position: absolute; inset-inline: 0; top: 0; height: 1.5px;
    background: linear-gradient(90deg, transparent, var(--cp-accent), transparent);
    animation: cpScanX 3.4s linear infinite;
}
@keyframes cpScanX { from { transform: translateX(-100%); } to { transform: translateX(100%); } }
.cp3-card:hover {
    border-color: rgba(129,140,248,.75);
    background: linear-gradient(150deg, rgba(180,175,255,.20), rgba(110,95,200,.08));
    box-shadow: 0 18px 44px rgba(10,7,26,.5), inset 0 1px 0 rgba(255,255,255,.35), 0 0 34px rgba(129,140,248,.35);
}
.cp3-card header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.cp3-card header i { font-size: 18px; color: var(--cp-accent); width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; background: rgba(129,140,248,.13); border: 1px solid rgba(129,140,248,.3); }
.cp3-card header h3 { margin: 0; font-size: 14px; font-weight: 700; color: var(--cp-dim); }
.cp3-card .cp-amount { font-size: 23px; font-weight: 800; color: var(--cp-ink); font-family: 'Cairo-Bold','Cairo',sans-serif; letter-spacing: .3px; }
.cp3-card .cp-sub { display: flex; gap: 12px; margin-top: 7px; font-size: 11.5px; color: var(--cp-dim); flex-wrap: wrap; }
.cp3-card .cp-sub b { color: var(--cp-accent); font-weight: 700; }
.cp3-card .cp-sub b.gold { color: var(--cp-gold); }
.cp3-card .cp-spark { margin-top: 9px; height: 32px; }
.cp3-card .cp-spark svg { width: 100%; height: 100%; display: block; }
.cp-cards-dim .cp3-card { opacity: .18; filter: blur(1.5px); pointer-events: none; }

/* ---------- answer panel (3D hologram on desktop, sheet on mobile) ---------- */
.cp3-panel {
    width: 470px; max-width: 92vw; border-radius: 20px; padding: 18px 22px;
    display: flex; flex-direction: column;
    background: linear-gradient(160deg, rgba(51,42,110,.55), rgba(22,17,55,.48));
    backdrop-filter: blur(20px) saturate(150%);
    -webkit-backdrop-filter: blur(20px) saturate(150%);
    border: 1px solid rgba(196,190,255,.32);
    box-shadow: 0 24px 60px rgba(10,7,26,.5), inset 0 1px 0 rgba(255,255,255,.22), 0 0 40px rgba(129,140,248,.10) inset;
    position: relative; overflow: hidden;
}
.cp3-panel.materialize { animation: cpMaterial .5s cubic-bezier(.2,.9,.3,1.1); }
@keyframes cpMaterial { 0% { opacity: 0; clip-path: inset(46% 0 46% 0); } 60% { clip-path: inset(0 0 0 0); } 100% { opacity: 1; } }
.cp3-panel::before {
    content: ''; position: absolute; inset-inline: 0; top: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--cp-accent), transparent);
    animation: cpScanX 2.6s linear infinite;
}
.cp-panel-head { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.cp-panel-head h3 { margin: 0; flex: 1; font-size: 16.5px; font-weight: 800; color: var(--cp-ink); font-family: 'Cairo-Bold','Cairo',sans-serif; }
.cp-icobtn {
    background: none; border: 1px solid rgba(165,171,212,.3); color: var(--cp-dim); border-radius: 10px;
    width: 32px; height: 32px; cursor: pointer; display: grid; place-items: center; font-size: 14px; transition: all .2s;
}
.cp-icobtn:hover { color: var(--cp-accent); border-color: var(--cp-accent); }
.cp-dates { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
.cp-date-chip {
    font-size: 11.5px; padding: 4px 12px; border-radius: 999px; color: var(--cp-gold);
    border: 1px solid rgba(251,191,36,.45); background: rgba(251,191,36,.08); letter-spacing: .3px;
}
.cp-date-chip b { color: var(--cp-ink); font-weight: 700; }
.cp-stat-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
.cp-pill { font-size: 12px; padding: 6px 14px; border-radius: 999px; background: rgba(129,140,248,.10); border: 1px solid rgba(129,140,248,.3); color: var(--cp-ink); }
.cp-pill b { color: var(--cp-accent); }
/* invoice rows — documents, not a grid: one tap target per invoice, opening
   the invoice list's own modal (Print / PDF come with it). */
.cp-invs { display: flex; flex-direction: column; gap: 7px; margin: 4px 0 10px; max-height: 33vh; overflow-y: auto; }
.cp-inv {
    display: flex; align-items: center; gap: 10px; text-decoration: none; cursor: pointer;
    border: 1px solid rgba(129,140,248,.28); border-radius: 11px; padding: 9px 12px;
    background: rgba(129,140,248,.07); transition: border-color .18s ease, background .18s ease;
}
.cp-inv:hover { border-color: var(--cp-accent); background: rgba(129,140,248,.16); text-decoration: none; }
.cp-inv-main { flex: 1 1 auto; min-width: 0; }
.cp-inv-main b { display: block; font-size: 13px; color: var(--cp-ink); font-weight: 700;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cp-inv-sub { display: block; font-size: 11px; color: var(--cp-dim); font-style: normal;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cp-inv-side { flex: 0 0 auto; text-align: <?= $is_arabic ? 'left' : 'right' ?>; }
.cp-inv-side b { display: block; font-size: 13px; color: var(--cp-accent); font-weight: 800; white-space: nowrap; }
.cp-tag { display: inline-block; font-size: 10px; font-style: normal; border-radius: 999px; padding: 1px 8px;
    margin-top: 3px; background: rgba(165,171,212,.14); color: var(--cp-dim); border: 1px solid rgba(165,171,212,.25); }
.cp-tag.is-paid { background: rgba(52,211,153,.14); color: #6ee7b7; border-color: rgba(52,211,153,.4); }
.cp-tag.is-partial { background: rgba(251,191,36,.14); color: #fcd34d; border-color: rgba(251,191,36,.4); }
.cp-tag.is-pending { background: rgba(248,113,113,.14); color: #fca5a5; border-color: rgba(248,113,113,.4); }
.cp-more { display: block; text-align: center; font-size: 12px; font-weight: 700; color: var(--cp-accent);
    text-decoration: none; padding: 7px; border-radius: 10px; background: rgba(129,140,248,.10); }
.cp-more:hover { background: rgba(129,140,248,.2); text-decoration: none; }
.cp-say { margin: 0 0 10px; font-size: 14px; line-height: 1.85; color: #dbe0ff; min-height: 48px; }
.cp-caret { display: inline-block; width: 8px; height: 15px; background: var(--cp-accent); vertical-align: -2px; animation: cpTwinkle 1s steps(2) infinite; }
.cp-chart { min-height: 0; position: relative; }
.cp-chart svg.cp-bars { width: 100%; height: 150px; display: block; }
.cp-legend { display: flex; gap: 12px; font-size: 11px; color: var(--cp-dim); justify-content: flex-end; margin-bottom: 2px; }
.cp-legend i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; margin-inline-end: 4px; vertical-align: -1px; }
.cp-panel-foot { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
.cp-cta {
    text-decoration: none; font-size: 13px; font-weight: 700; padding: 9px 20px; border-radius: 12px;
    color: #1a1442; background: linear-gradient(135deg, var(--cp-accent), var(--cp-accent2));
    box-shadow: 0 6px 20px rgba(129,140,248,.35); transition: transform .2s, box-shadow .2s;
}
.cp-cta:hover { transform: translateY(-2px); color: #1a1442; box-shadow: 0 10px 26px rgba(129,140,248,.5); }

/* answer hologram host — 2D overlay, always fully visible, never clipped by 3D projection */
.cp-panel-host {
    position: absolute; z-index: 40; display: none; align-items: center; pointer-events: none;
    inset-inline-start: 22px; top: 84px; bottom: 128px; width: min(480px, 42%);
}
.cp-panel-host.on { display: flex; }
.cp-panel-host .cp3-panel { pointer-events: auto; width: 100%; max-height: 100%; overflow-y: auto; scrollbar-width: thin; }
@media (max-width: 767px) {
    .cp-panel-host { inset-inline: 3%; width: auto; top: 70px; bottom: 118px; align-items: flex-end; }
    .cp-panel-host .cp3-panel { max-height: 60vh; }
}

/* ---------- command console ---------- */
.cp-command {
    position: absolute; z-index: 30; inset-inline: 0; bottom: 0;
    padding: 10px 18px 16px;
    background: linear-gradient(to top, rgba(12,9,32,.96) 55%, transparent);
    display: flex; flex-direction: column; gap: 9px; align-items: center;
    transform: perspective(900px) rotateX(calc(var(--cp-my, 0) * 1.2deg)) rotateY(calc(var(--cp-mx, 0) * -1.6deg));
}
.cp-ledger { display: none; gap: 8px; max-width: 100%; overflow-x: auto; padding: 2px; scrollbar-width: none; }
.cp-ledger.has { display: flex; }
.cp-ledger::-webkit-scrollbar { display: none; }
.cp-mini {
    flex: 0 0 auto; display: flex; align-items: center; gap: 8px; cursor: pointer;
    font-size: 11.5px; color: var(--cp-dim); padding: 6px 14px; border-radius: 999px;
    background: rgba(255,255,255,.05); border: 1px solid rgba(165,171,212,.25);
    transition: all .25s; animation: cpMiniIn .4s ease backwards;
}
@keyframes cpMiniIn { from { opacity: 0; transform: translateY(10px); } }
.cp-mini:hover { border-color: var(--cp-accent); color: var(--cp-ink); }
.cp-mini i { color: var(--cp-accent); }
.cp-chips { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; max-width: 100%; }
.cp-chip {
    font-size: 12.5px; padding: 7px 16px; border-radius: 999px; cursor: pointer; user-select: none;
    color: var(--cp-ink); background: rgba(129,140,248,.08); border: 1px solid rgba(129,140,248,.35);
    transition: all .25s; white-space: nowrap;
}
.cp-chip:hover { background: rgba(129,140,248,.22); box-shadow: 0 0 16px rgba(129,140,248,.3); transform: translateY(-2px); }
.cp-capsule {
    /* !important shields against the host app's global form/footer styling */
    display: flex !important; visibility: visible !important; align-items: center; gap: 10px;
    width: min(680px, 96%); padding: 8px 10px; border-radius: 999px;
    background: linear-gradient(150deg, rgba(255,255,255,.10), rgba(255,255,255,.04));
    border: 1px solid rgba(129,140,248,.4);
    backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
    box-shadow: 0 12px 34px rgba(10,7,26,.55), 0 0 24px rgba(129,140,248,.10);
    transition: box-shadow .3s, border-color .3s;
}
.cp-capsule:focus-within { border-color: var(--cp-accent); box-shadow: 0 12px 34px rgba(10,7,26,.55), 0 0 34px rgba(129,140,248,.35); }
.cp-capsule.attn { animation: cpAttn 2s ease-in-out 3; }
@keyframes cpAttn { 50% { border-color: var(--cp-accent); box-shadow: 0 12px 34px rgba(10,7,26,.55), 0 0 46px rgba(129,140,248,.55); transform: scale(1.015); } }
.cp-core {
    flex: 0 0 38px; width: 38px; height: 38px; border-radius: 50%;
    background: radial-gradient(circle at 35% 30%, #c7d2fe, var(--cp-accent) 45%, #312e81 100%);
    box-shadow: 0 0 18px rgba(129,140,248,.75);
    animation: cpCore 2.8s ease-in-out infinite;
}
@keyframes cpCore { 50% { box-shadow: 0 0 30px rgba(129,140,248,1); transform: scale(1.07); } }
.cp-core.think { animation-duration: .7s; }
.cp-capsule input {
    flex: 1; min-width: 0; background: transparent; border: 0; outline: 0;
    color: var(--cp-ink); font-size: 14.5px; font-family: inherit; padding: 8px 4px;
}
.cp-capsule input::placeholder { color: rgba(165,171,212,.65); }
.cp-wave { display: none; align-items: center; gap: 3px; height: 22px; }
.cp-wave.on { display: flex; }
.cp-wave i { width: 3px; border-radius: 2px; background: var(--cp-accent); animation: cpWave 1s ease-in-out infinite; }
.cp-wave i:nth-child(1) { height: 8px; } .cp-wave i:nth-child(2) { height: 16px; animation-delay: .12s; }
.cp-wave i:nth-child(3) { height: 22px; animation-delay: .24s; } .cp-wave i:nth-child(4) { height: 14px; animation-delay: .36s; }
.cp-wave i:nth-child(5) { height: 7px; animation-delay: .48s; }
@keyframes cpWave { 50% { transform: scaleY(.35); opacity: .6; } }
.cp-send {
    flex: 0 0 40px; width: 40px; height: 40px; border-radius: 50%; border: 0; cursor: pointer;
    display: grid; place-items: center; color: #1a1442;
    background: linear-gradient(135deg, var(--cp-accent), var(--cp-accent2));
    box-shadow: 0 4px 16px rgba(129,140,248,.4); transition: transform .2s;
}
.cp-send:hover { transform: scale(1.08); }
.cp-send:disabled { opacity: .5; cursor: wait; }
.cp-send svg { transform: scaleX(<?= $flip ?>); }

/* ---------- loader ---------- */
.cp-loader {
    position: absolute; inset: 0; z-index: 50; display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 18px;
    background: radial-gradient(100% 80% at 50% 20%, #1d1650, #0b0820 80%);
    transition: opacity .7s, visibility .7s;
}
.cp-loader.off { opacity: 0; visibility: hidden; }
.cp-loader-ring { position: relative; width: 96px; height: 96px; }
.cp-loader-ring::before, .cp-loader-ring::after {
    content: ''; position: absolute; inset: 0; border-radius: 50%;
    border: 2px solid transparent; border-top-color: var(--cp-accent);
}
.cp-loader-ring::before { animation: cpSpinCSS 1.1s linear infinite; }
.cp-loader-ring::after { inset: 14%; border-top-color: var(--cp-gold); animation: cpSpinCSS .8s linear infinite reverse; }
@keyframes cpSpinCSS { to { transform: rotate(360deg); } }
.cp-load-pct { position: absolute; inset: 0; display: grid; place-items: center; font-size: 18px; font-weight: 800; color: var(--cp-ink); }
.cp-loader p { margin: 0; color: var(--cp-dim); font-size: 13.5px; letter-spacing: .4px; animation: cpTwinkle 2s infinite; }

/* ---------- responsive ---------- */
@media (max-width: 991px) {
    .cp-deck { height: calc(100vh - 170px); min-height: 560px; }
    .cp-hud-core { display: none; }
    .cp-chips { flex-wrap: nowrap; overflow-x: auto; justify-content: flex-start; scrollbar-width: none; padding: 2px; }
    .cp-chips::-webkit-scrollbar { display: none; }
}
@media (max-width: 575px) {
    .cp-wrap { padding: 6px 6px 12px; }
    .cp-deck { border-radius: 18px; }
    .cp-hud { padding: 12px 14px; }
    .cp-hud-id h1 { font-size: 16px; }
    .cp-say { font-size: 13px; }
}
@media (prefers-reduced-motion: reduce) {
    .cp-deck *, .cp-deck *::before, .cp-deck *::after { animation-duration: .01s !important; animation-iteration-count: 1 !important; transition-duration: .01s !important; }
}
/* Saudi Riyal symbol — inherits the surrounding text color, scales with font */
.cp-riyal { width: .73em; height: .82em; display: inline-block; vertical-align: -.09em; fill: currentColor; margin-inline-start: 3px; }

/* ================= LIGHT THEME — matches the site (Spark) surface ================= */
.cp-deck {
    --cp-ink: #0f172a;
    --cp-dim: #64748b;
    background: radial-gradient(120% 90% at 50% -10%, #ffffff 0%, #f8fafc 45%, #eef2ff 100%);
    box-shadow: 0 0 0 1px #e2e8f0, 0 18px 44px rgba(15,23,42,.10);
    color: #0f172a;
}
.cp-orb1 { background: rgba(99,102,241,.16); }
.cp-orb2 { background: rgba(139,92,246,.14); }
.cp-orb3 { background: rgba(245,158,11,.08); }
.cp-hud { background: linear-gradient(to bottom, rgba(255,255,255,.92), transparent); }
.cp-hud-id h1, .cp-panel-head h3, .cp3-card .cp-amount { color: #0f172a; }
.cp-hud-id p, .cp-hud-status, .cp-hint, .cp-legend, .cp3-card header h3, .cp3-card .cp-sub { color: #64748b; }
.cp-hud-core { border-color: #c7d2fe; color: #4338ca; background: #eef2ff; }
.cp-voice { border-color: #cbd5e1; color: #475569; background: #fff; }
.cp-voice.on { color: #fff; background: #6366f1; border-color: #6366f1; box-shadow: 0 0 12px rgba(99,102,241,.5); }
.cp3-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 22px rgba(15,23,42,.08);
}
.cp3-card header i { background: #eef2ff; border-color: #c7d2fe; color: #6366f1; }
.cp3-card .cp-sub b { color: #4338ca; }
.cp3-card .cp-sub b.gold { color: #b45309; }
.cp-panel, .cp-panel-host > * {
    background: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 12px 30px rgba(15,23,42,.10);
}
.cp-say { color: #475569; }
.cp-icobtn { border-color: #e2e8f0; color: #64748b; background: #fff; }
.cp-icobtn:hover { color: #4338ca; border-color: #818cf8; }
.cp-date-chip { color: #92400e; background: #fffbeb; border-color: #fde68a; }
.cp-date-chip b { color: #0f172a; }
.cp-pill { background: #f8fafc; border-color: #e2e8f0; color: #0f172a; }
.cp-pill b { color: #4338ca; }
.cp-inv { background: #fff; border-color: #e2e8f0; }
.cp-inv:hover { background: #f8faff; border-color: #818cf8; }
.cp-inv-main b { color: #0f172a; }
.cp-inv-sub { color: #64748b; }
.cp-inv-side b { color: #4338ca; }
.cp-tag { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
.cp-tag.is-paid { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
.cp-tag.is-partial { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.cp-tag.is-pending { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
.cp-more { background: #f8fafc; color: #4338ca; }
.cp-more:hover { background: #eef2ff; }
.cp-mini, .cp-chip { background: #fff; border-color: #e2e8f0; color: #475569; }
.cp-mini:hover, .cp-chip:hover { border-color: #818cf8; color: #4338ca; }
.cp-capsule {
    background: #fff; border: 1px solid #e2e8f0;
    box-shadow: 0 10px 28px rgba(15,23,42,.12);
}
.cp-capsule:focus-within { border-color: #6366f1; box-shadow: 0 10px 28px rgba(15,23,42,.12), 0 0 0 3px rgba(99,102,241,.18); }
.cp-capsule input { color: #0f172a; }
.cp-capsule input::placeholder { color: #94a3b8; }
.cp-loader { background: radial-gradient(100% 80% at 50% 20%, #ffffff, #f1f5f9 80%); }
.cp-loader p, .cp-load-pct { color: #475569; }
.cp-send { color: #fff; }
/* mic button */
.cp-mic {
    background: #fff; border: 1px solid #e2e8f0; color: #475569;
    width: 34px; height: 34px; border-radius: 50%; cursor: pointer;
    display: grid; place-items: center; flex: 0 0 34px; transition: .15s;
}
.cp-mic:hover { border-color: #818cf8; color: #4338ca; }
.cp-mic.rec { background: #ef4444; border-color: #ef4444; color: #fff; animation: cpMicPulse 1.1s infinite; }
@keyframes cpMicPulse { 50% { box-shadow: 0 0 0 7px rgba(239,68,68,.18); } }
/* cards 20% smaller */
.cp3-card { width: 240px; padding: 13px 14px; border-radius: 15px; }
.cp3-card .cp-amount { font-size: 18.5px; }
.cp3-card header i { width: 28px; height: 28px; font-size: 15px; }
.cp3-card header h3 { font-size: 12px; }
.cp3-card .cp-sub {
    display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px; font-size: 10.5px; margin-top: 9px;
}
.cp3-card .cp-sub > span {
    background: #f8fafc; border: 1px solid #eef2f7; border-radius: 8px;
    padding: 5px 8px; display: flex; flex-direction: column; gap: 2px;
    min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.cp3-card .cp-sub > span:nth-child(3) { grid-column: 1 / -1; }
.cp3-card .cp-sub b { font-size: 12px; }
/* stop sub-pixel shimmer on the 3D-positioned holders */
.cp-css3d .cp3-holder, .cp3-card { backface-visibility: hidden; -webkit-backface-visibility: hidden; will-change: transform; }
/* hover kept the dark theme's translucent fill, so the card behind showed through */
.cp3-card { background: #fff; backdrop-filter: none; -webkit-backdrop-filter: none; }
.cp3-card:hover {
    background: #fff;
    border-color: #818cf8;
    box-shadow: 0 18px 44px rgba(15,23,42,.18), 0 0 0 3px rgba(99,102,241,.14);
}
/* and lift the hovered card above its neighbours */
.cp-css3d .cp3-holder:hover { z-index: 999 !important; }
/* command bar: was a dark slab sitting on the light deck */
.cp-command { background: linear-gradient(to top, rgba(248,250,252,.97) 55%, transparent); }
.cp-panel, .cp3-panel { background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 12px 30px rgba(15,23,42,.10); }
</style>

<!-- Saudi Riyal glyph sprite (referenced via <use href="#cpRiyal">) -->
<svg width="0" height="0" style="position:absolute;overflow:hidden" aria-hidden="true" focusable="false"><symbol id="cpRiyal" viewBox="0 0 1124.14 1256.39"><path d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></symbol></svg>

<div class="cp-wrap">
  <div class="cp-deck" id="cpDeck" dir="<?= $dir ?>">

    <div class="cp-aurora"><i class="cp-orb1"></i><i class="cp-orb2"></i><i class="cp-orb3"></i></div>

    <div class="cp-gl" id="cpGL"></div>
    <div class="cp-css3d" id="cpCSS"></div>

    <header class="cp-hud">
      <div class="cp-hud-id">
        <span class="cp-hud-logo"><i class="fa fa-diamond"></i></span>
        <div>
          <h1><?= $t['title'] ?></h1>
          <p><?= $t['subtitle'] ?></p>
        </div>
      </div>
      <div class="cp-hud-status">
        <span><span class="cp-dot"></span> <?= $t['online'] ?></span>
        <span class="cp-hud-core"><?= $t['core'] ?> · <span id="cpClock">--:--</span></span>
        <button type="button" class="cp-voice" id="cpVoice" title="<?= $t['voice_off'] ?>"><i class="fa fa-volume-off"></i></button>
      </div>
    </header>
    <div class="cp-hint"><i class="mdi mdi-cursor-move"></i> <?= $t['drag_hint'] ?></div>

    <div class="cp-panel-host" id="cpPanelHost"></div>

    <div class="cp-command">
      <div class="cp-ledger" id="cpLedger"></div>
      <div class="cp-chips" id="cpChips">
        <span class="cp-chip"><?= $t['chip1'] ?></span>
        <span class="cp-chip"><?= $t['chip2'] ?></span>
        <span class="cp-chip"><?= $t['chip5'] ?></span>
        <span class="cp-chip"><?= $t['chip3'] ?></span>
        <span class="cp-chip"><?= $t['chip4'] ?></span>
      </div>
      <div class="cp-capsule" id="cpForm">
        <span class="cp-core" id="cpCore"></span>
        <input type="text" id="cpInput" autocomplete="off" placeholder="<?= $t['placeholder'] ?>" aria-label="<?= $t['title'] ?>">
        <button type="button" class="cp-mic" id="cpMic" title="<?= $is_ar ? 'اسأل بالصوت' : 'Ask by voice' ?>" aria-label="voice">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3z"/><path d="M19 11a1 1 0 1 0-2 0 5 5 0 0 1-10 0 1 1 0 1 0-2 0 7 7 0 0 0 6 6.92V21a1 1 0 1 0 2 0v-3.08A7 7 0 0 0 19 11z"/></svg>
        </button>
        <span class="cp-wave" id="cpWave"><i></i><i></i><i></i><i></i><i></i></span>
        <button type="button" class="cp-send" id="cpSend" aria-label="send">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/></svg>
        </button>
      </div>
    </div>

    <div class="cp-loader" id="cpLoader">
      <div class="cp-loader-ring"><span class="cp-load-pct" id="cpLoadPct">0%</span></div>
      <p><?= $t['loading'] ?></p>
    </div>

  </div>
</div>

<script type="importmap">
{ "imports": { "three": "<?= base_url('assets/js/vendor/three/three.module.min.js') ?>" } }
</script>

<script>
/* Shared UI state + data (used by both the 3D module and fallbacks) */
window.CP = {
    booted: false,
    isAr: <?= $is_ar ? 'true' : 'false' ?>,
    flip: <?= $flip ?>,
    curr: <?= json_encode($curr) ?>,
    year: <?= (int) date('Y') ?>,
    t: <?= json_encode($t, JSON_UNESCAPED_UNICODE) ?>,
    glb: <?= json_encode(base_url('assets/glb/saudi_businessman_web.glb')) ?>,
    totals: {
        sales:     { total: <?= $salesTotal ?>, paid: <?= $salesPaid ?>, due: <?= $salesDue ?>, count: <?= $salesCount ?> },
        purchases: { total: <?= $purchTotal ?>, paid: <?= $purchPaid ?>, due: <?= $purchDue ?>, count: <?= $purchCount ?> },
        tax:       { sales: <?= $taxSales ?>, purchases: <?= $taxPurchases ?> }
    },
    monthly: {
        sales:        <?= json_encode(array_values($mSales)) ?>,
        salesTax:     <?= json_encode(array_values($mSalesTax)) ?>,
        purchases:    <?= json_encode(array_values($mPurch)) ?>,
        purchasesTax: <?= json_encode(array_values($mPurchTax)) ?>
    },
    links: {
        sales: <?= json_encode(admin_url('sales')) ?>,
        purchases: <?= json_encode(admin_url('purchases')) ?>,
        tax: <?= json_encode(admin_url('reports/tax')) ?>
    },
    askEndpoint: <?= json_encode(admin_url('welcome/copilot_ask')) ?>
};
/* safety: if the 3D module can't boot (very old browser), never trap the loader */
setTimeout(function () {
    if (!window.CP.booted) {
        var l = document.getElementById('cpLoader');
        if (l) { l.classList.add('off'); }
    }
}, 20000);
</script>

<script type="module">
import * as THREE from 'three';
import { GLTFLoader } from '<?= base_url('assets/js/vendor/three/GLTFLoader.js') ?>';
import { CSS3DRenderer, CSS3DObject } from '<?= base_url('assets/js/vendor/three/CSS3DRenderer.js') ?>';
import { RoomEnvironment } from '<?= base_url('assets/js/vendor/three/RoomEnvironment.js') ?>';

const CP = window.CP;
const T = CP.t;
const FLIP = CP.flip;
const MONTHS_AR = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
const MONTHS_EN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const MONTHS = CP.isAr ? MONTHS_AR : MONTHS_EN;
const $ = (id) => document.getElementById(id);

const deck = $('cpDeck'), glHost = $('cpGL'), cssHost = $('cpCSS');

/* ============================ helpers ============================ */
/* Saudi Riyal is shown as its official symbol (SVG), never "SR"/"SAR", in any language. */
const RIYAL_SVG = '<svg class="cp-riyal" aria-hidden="true" focusable="false"><use href="#cpRiyal"></use></svg>';
function isSaudiCurr() {
    const c = (CP.curr || '').toString().trim().toLowerCase();
    return c === 'sr' || c === 'sar' || c === 'ر.س' || c === '﷼';
}
function fmt(n) {
    const s = new Intl.NumberFormat('en-US', { maximumFractionDigits: 2, minimumFractionDigits: 2 }).format(n || 0);
    if (isSaudiCurr()) { return s + RIYAL_SVG; }
    return CP.isAr ? (s + ' ' + CP.curr) : (CP.curr + ' ' + s);
}
/* Plain-text money (no markup) — for contexts that can't render HTML, e.g. SVG <title> tooltips. */
function fmtPlain(n) {
    const s = new Intl.NumberFormat('en-US', { maximumFractionDigits: 2, minimumFractionDigits: 2 }).format(n || 0);
    if (isSaudiCurr()) { return s; }
    return CP.isAr ? (s + ' ' + CP.curr) : (CP.curr + ' ' + s);
}
function fmtDate(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}
function glowTexture(hex, inner = 0.05) {
    const c = document.createElement('canvas'); c.width = c.height = 256;
    const g = c.getContext('2d');
    const grad = g.createRadialGradient(128, 128, 256 * inner, 128, 128, 128);
    grad.addColorStop(0, hex + 'ff'); grad.addColorStop(0.4, hex + '55'); grad.addColorStop(1, hex + '00');
    g.fillStyle = grad; g.fillRect(0, 0, 256, 256);
    const tx = new THREE.CanvasTexture(c); tx.colorSpace = THREE.SRGBColorSpace;
    return tx;
}
function sparkSVG(values, color) {
    const w = 100, h = 32;
    /* include 0 so the baseline is always in frame, and span min..max so
       negative values (e.g. net VAT refund) render instead of clipping */
    const mx = Math.max(...values, 0), mn = Math.min(...values, 0);
    const span = (mx - mn) || 1;
    const yOf = (v) => (h - 3 - ((v - mn) / span) * (h - 8));
    const pts = values.map((v, i) => (i * (w / (values.length - 1))).toFixed(1) + ',' + yOf(v).toFixed(1)).join(' ');
    const y0 = yOf(0).toFixed(1);
    return '<svg viewBox="0 0 ' + w + ' ' + h + '" preserveAspectRatio="none">' +
        (mn < 0 ? '<line x1="0" y1="' + y0 + '" x2="' + w + '" y2="' + y0 + '" stroke="' + color + '" stroke-width="0.6" opacity="0.35" stroke-dasharray="2 2"/>' : '') +
        '<polyline points="' + pts + '" fill="none" stroke="' + color + '" stroke-width="1.6" stroke-linejoin="round"/>' +
        '<polygon points="0,' + y0 + ' ' + pts + ' ' + w + ',' + y0 + '" fill="' + color + '" opacity="0.12"/></svg>';
}

/* ============================ 3D world ============================ */
let renderer, cssRenderer, scene, camera, avatarGroup, cardsGroup, particleGroup;
let ringA, ringB, halo, beam, discGlow;
const tweens = [];
function addTween(from, to, dur, onUpdate, ease = (p) => 1 - Math.pow(1 - p, 3), onDone) {
    tweens.push({ from, to, dur, onUpdate, ease, onDone, t0: performance.now() });
}

try {
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
} catch (e) {
    document.querySelector('.cp-loader p').textContent = T['no_webgl'];
    setTimeout(() => $('cpLoader').classList.add('off'), 1500);
    throw e;
}
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.05;
glHost.appendChild(renderer.domElement);

cssRenderer = new CSS3DRenderer();
cssRenderer.domElement.style.position = 'absolute';
cssRenderer.domElement.style.inset = '0';
cssHost.appendChild(cssRenderer.domElement);

scene = new THREE.Scene();
scene.fog = new THREE.Fog(0xeef2ff, 9, 20);

camera = new THREE.PerspectiveCamera(38, 1, 0.1, 60);

const pmrem = new THREE.PMREMGenerator(renderer);
scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;

const key = new THREE.DirectionalLight(0xfff2e0, 1.6); key.position.set(2.5, 4, 2.5); scene.add(key);
const rim = new THREE.PointLight(0x818cf8, 30, 12); rim.position.set(-2.4, 2.4, -2.2); scene.add(rim);
const gold = new THREE.PointLight(0xfbbf24, 12, 10); gold.position.set(2.2, 1.2, -1.6); scene.add(gold);

/* --- holographic platform --- */
const platform = new THREE.Group();
discGlow = new THREE.Mesh(
    new THREE.PlaneGeometry(4.6, 4.6),
    new THREE.MeshBasicMaterial({ map: glowTexture('#818cf8'), transparent: true, opacity: 0.5, blending: THREE.AdditiveBlending, depthWrite: false })
);
discGlow.rotation.x = -Math.PI / 2; discGlow.position.y = 0.01;
platform.add(discGlow);

ringA = new THREE.Mesh(
    new THREE.RingGeometry(1.06, 1.10, 96, 1, 0, Math.PI * 1.4),
    new THREE.MeshBasicMaterial({ color: 0x818cf8, transparent: true, opacity: 0.8, blending: THREE.AdditiveBlending, side: THREE.DoubleSide, depthWrite: false })
);
ringA.rotation.x = -Math.PI / 2; ringA.position.y = 0.02;
platform.add(ringA);

ringB = new THREE.Mesh(
    new THREE.RingGeometry(1.3, 1.315, 96, 1, 0, Math.PI * 1.7),
    new THREE.MeshBasicMaterial({ color: 0xfbbf24, transparent: true, opacity: 0.45, blending: THREE.AdditiveBlending, side: THREE.DoubleSide, depthWrite: false })
);
ringB.rotation.x = -Math.PI / 2; ringB.position.y = 0.02;
platform.add(ringB);

const grid = new THREE.PolarGridHelper(6, 20, 10, 64, 0x6366f1, 0x94a3b8);
grid.material.transparent = true; grid.material.opacity = 0.16;
platform.add(grid);
scene.add(platform);

/* --- floating particles --- */
particleGroup = new THREE.Group();
{
    const N = 320, pos = new Float32Array(N * 3);
    for (let i = 0; i < N; i++) {
        const r = 1.3 + Math.random() * 4.2, a = Math.random() * Math.PI * 2;
        pos[i * 3] = Math.cos(a) * r;
        pos[i * 3 + 1] = Math.random() * 3.4;
        pos[i * 3 + 2] = Math.sin(a) * r;
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    const pts = new THREE.Points(geo, new THREE.PointsMaterial({
        color: 0x6366f1, size: 0.03, transparent: true, opacity: 0.95,
        blending: THREE.AdditiveBlending, depthWrite: false, sizeAttenuation: true
    }));
    particleGroup.add(pts);
    const pts2 = pts.clone();
    pts2.material = pts.material.clone(); pts2.material.color.set(0xfbbf24); pts2.material.opacity = 0.35;
    pts2.rotation.y = 2.1; pts2.scale.setScalar(0.85);
    particleGroup.add(pts2);
}
scene.add(particleGroup);

/* --- thinking halo (spins over the avatar's head while processing) --- */
halo = new THREE.Mesh(
    new THREE.TorusGeometry(0.3, 0.008, 12, 64),
    new THREE.MeshBasicMaterial({ color: 0x818cf8, transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false })
);
halo.position.set(0, 1.93, 0); halo.rotation.x = Math.PI / 2.15;
scene.add(halo);

/* --- projection beam (avatar → answer panel) --- */
{
    const geo = new THREE.CylinderGeometry(0.02, 0.26, 1, 24, 1, true);
    geo.translate(0, 0.5, 0);
    beam = new THREE.Mesh(geo, new THREE.MeshBasicMaterial({
        color: 0x818cf8, transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false, side: THREE.DoubleSide
    }));
    scene.add(beam);
}

/* ============================ camera rig ============================ */
const rig = {
    az: 0, pol: 1.32, r: 5.0,
    dAz: 0, dPol: 1.32, dR: 5.0,
    tgt: new THREE.Vector3(0, 1.02, 0),
    dTgt: new THREE.Vector3(0, 1.02, 0),
    mx: 0, my: 0, dragging: false
};
function baseRadius() { return deck.clientWidth < 768 ? 6.3 : (deck.clientWidth < 1100 ? 5.6 : 5.0); }

glHost.addEventListener('pointerdown', (e) => {
    rig.dragging = true; rig.px = e.clientX; rig.py = e.clientY;
    glHost.classList.add('grabbing'); glHost.setPointerCapture(e.pointerId);
});
glHost.addEventListener('pointermove', (e) => {
    const r = deck.getBoundingClientRect();
    rig.mx = ((e.clientX - r.left) / r.width - 0.5) * 2;
    rig.my = ((e.clientY - r.top) / r.height - 0.5) * 2;
    deck.style.setProperty('--cp-mx', rig.mx.toFixed(3));
    deck.style.setProperty('--cp-my', rig.my.toFixed(3));
    if (rig.dragging) {
        rig.dAz -= (e.clientX - rig.px) * 0.006;
        rig.dPol = Math.min(1.62, Math.max(1.08, rig.dPol - (e.clientY - rig.py) * 0.004));
        rig.dAz = Math.min(1.15, Math.max(-1.15, rig.dAz));
        rig.px = e.clientX; rig.py = e.clientY;
    }
});
['pointerup', 'pointercancel', 'pointerleave'].forEach((ev) =>
    glHost.addEventListener(ev, () => { rig.dragging = false; glHost.classList.remove('grabbing'); })
);

/* ============================ avatar ============================ */
const loadMgr = new THREE.LoadingManager();
loadMgr.onProgress = (u, loaded, total) => {
    $('cpLoadPct').textContent = Math.min(99, Math.round((loaded / Math.max(total, 1)) * 100)) + '%';
};
new GLTFLoader(loadMgr).load(CP.glb,
    (gltf) => {
        avatarGroup = new THREE.Group();
        const model = gltf.scene;
        model.updateMatrixWorld(true);
        const box = new THREE.Box3().setFromObject(model);
        const size = box.getSize(new THREE.Vector3());
        /* target height 1.82 world units; clamp hard so a bad bbox
           (skinned/quantized exports) can never explode the scale */
        let scale = (size.y > 0.01 && isFinite(size.y)) ? 1.82 / size.y : 1.86;
        scale = Math.min(Math.max(scale, 0.02), 20);
        model.scale.setScalar(scale);
        box.setFromObject(model);
        if (isFinite(box.min.y)) {
            model.position.y = -box.min.y;
            model.position.x = -(box.min.x + box.max.x) / 2;
            model.position.z = -(box.min.z + box.max.z) / 2;
        }
        avatarGroup.add(model);
        scene.add(avatarGroup);
        addTween(0, 1, 900, (v) => { avatarGroup.scale.setScalar(0.6 + 0.4 * v); avatarGroup.position.y = (1 - v) * 0.5; });
        bootUI();
    },
    (e) => {
        if (e.total) { $('cpLoadPct').textContent = Math.min(99, Math.round((e.loaded / e.total) * 100)) + '%'; }
    },
    () => { bootUI(); } /* even if the model fails, the world must live */
);

/* ============================ 3D UI (CSS3D) ============================ */
const CARD_SCALE = 0.0042;
cardsGroup = new THREE.Group();
scene.add(cardsGroup);

function makeCSS3D(el, az, radius, height) {
    const holder = document.createElement('div');
    holder.className = 'cp3-holder';
    holder.dir = CP.isAr ? 'rtl' : 'ltr';
    holder.style.left = '0'; holder.style.top = '0'; /* pin static position: RTL must not shift the 3D origin */
    holder.appendChild(el);
    const obj = new CSS3DObject(holder);
    obj.scale.setScalar(CARD_SCALE);
    obj.userData.az = az; obj.userData.radius = radius; obj.userData.height = height;
    return obj;
}
function polarPlace(obj, orbit = 0) {
    const az = obj.userData.az + orbit;
    obj.position.set(Math.sin(az) * obj.userData.radius, obj.userData.height, Math.cos(az) * obj.userData.radius);
    obj.quaternion.copy(camera.quaternion); /* billboard: always face the viewer */
}

const cardDefs = [
    {
        intent: CP.isAr ? 'المبيعات' : 'sales', icon: 'mdi mdi-finance', title: T['sales'],
        amount: CP.totals.sales.total, series: CP.monthly.sales, color: '#818cf8',
        az: FLIP * 0.72, h: 1.92, r: 2.1,
        sub: '<span>' + T['received'] + ' <b>' + fmt(CP.totals.sales.paid) + '</b></span><span>' + T['due'] + ' <b class="gold">' + fmt(CP.totals.sales.due) + '</b></span><span><b>' + CP.totals.sales.count + '</b> ' + T['invoices'] + '</span>'
    },
    {
        intent: CP.isAr ? 'المشتريات' : 'purchases', icon: 'mdi mdi-chart-areaspline', title: T['purchases'],
        amount: CP.totals.purchases.total, series: CP.monthly.purchases, color: '#a78bfa',
        az: FLIP * 1.15, h: 1.14, r: 2.25,
        sub: '<span>' + T['received'] + ' <b>' + fmt(CP.totals.purchases.paid) + '</b></span><span>' + T['due'] + ' <b class="gold">' + fmt(CP.totals.purchases.due) + '</b></span><span><b>' + CP.totals.purchases.count + '</b> ' + T['invoices'] + '</span>'
    },
    {
        intent: CP.isAr ? 'الضريبة' : 'tax', icon: 'mdi mdi-percent', title: T['tax'],
        amount: CP.totals.tax.sales - CP.totals.tax.purchases,
        series: CP.monthly.salesTax.map((v, i) => v - CP.monthly.purchasesTax[i]), color: '#fbbf24',
        az: FLIP * -0.62, h: 1.55, r: 2.15,
        sub: '<span>' + T['vat_out'] + ' <b>' + fmt(CP.totals.tax.sales) + '</b></span><span>' + T['vat_in'] + ' <b>' + fmt(CP.totals.tax.purchases) + '</b></span><span>' + T['vat_net'] + ' <b class="gold">' + fmt(CP.totals.tax.sales - CP.totals.tax.purchases) + '</b></span>'
    }
];
let hoverPause = false;
cardDefs.forEach((d, i) => {
    const el = document.createElement('div');
    el.className = 'cp3-card';
    el.innerHTML =
        '<header><i class="' + d.icon + '"></i><h3>' + d.title + '</h3></header>' +
        '<div class="cp-amount">0</div>' +
        '<div class="cp-sub">' + d.sub + '</div>' +
        '<div class="cp-spark">' + sparkSVG(d.series, d.color) + '</div>';
    el.addEventListener('click', () => ask(d.intent));
    el.addEventListener('mouseenter', () => { hoverPause = true; });
    el.addEventListener('mouseleave', () => { hoverPause = false; });
    const obj = makeCSS3D(el, d.az, d.r, d.h);
    obj.userData.el = el; obj.userData.def = d; obj.userData.idx = i;
    obj.scale.setScalar(0.0001);
    cardsGroup.add(obj);
});
function animateCounters() {
    cardsGroup.children.forEach((obj) => {
        const el = obj.userData.el.querySelector('.cp-amount');
        addTween(0, obj.userData.def.amount, 1500, (v) => { el.innerHTML = fmt(v); });
    });
}

/* --- answer panel: a hologram object in the same world --- */
const panelEl = document.createElement('div');
panelEl.className = 'cp3-panel';
panelEl.innerHTML =
    '<header class="cp-panel-head"><h3 id="cpPanelTitle"></h3>' +
    '<button type="button" class="cp-icobtn" id="cpSpeak" title="' + T['listen'] + '"><i class="fa fa-volume-up"></i></button>' +
    '<button type="button" class="cp-icobtn" id="cpClose"><i class="fa fa-times"></i></button></header>' +
    '<div class="cp-dates" id="cpDates"></div>' +
    '<div class="cp-stat-pills" id="cpPills"></div>' +
    '<p class="cp-say" id="cpSay"></p>' +
    '<div id="cpRows"></div>' +
    '<div class="cp-chart" id="cpChart"></div>' +
    '<div class="cp-panel-foot" id="cpFoot"></div>';
$('cpPanelHost').appendChild(panelEl);
let panelOpen = false;
/* the beam fires from the avatar toward the hologram's side of the stage */
const beamTarget = new THREE.Vector3(FLIP * -2.2, 1.35, 0.7);

function isMobile() { return deck.clientWidth < 768; }

/* ============================ render loop ============================ */
let orbitAngle = 0, thinking = false;
const clock = new THREE.Clock();

function fitDeck() {
    /* the deck must always end above the fold: the command capsule is vital.
       getBoundingClientRect includes margins the offsetTop chain misses. */
    const docTop = deck.getBoundingClientRect().top + (window.scrollY || window.pageYOffset || 0);
    deck.style.height = Math.max(520, window.innerHeight - docTop - 14) + 'px';
}
function resize() {
    fitDeck();
    const w = deck.clientWidth, h = deck.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
    cssRenderer.setSize(w, h);
    rig.dR = baseRadius();
}
window.addEventListener('resize', resize);
window.addEventListener('load', resize);
resize();
setTimeout(resize, 600); setTimeout(resize, 1800); /* late layout shifts (fonts, site scripts) */

function animate() {
    requestAnimationFrame(animate);
    const dt = clock.getDelta(), t = clock.elapsedTime;
    const now = performance.now();

    /* tweens */
    for (let i = tweens.length - 1; i >= 0; i--) {
        const tw = tweens[i];
        const p = Math.min((now - tw.t0) / tw.dur, 1);
        tw.onUpdate(tw.from + (tw.to - tw.from) * tw.ease(p));
        if (p >= 1) { if (tw.onDone) { tw.onDone(); } tweens.splice(i, 1); }
    }

    /* camera rig: inertia + mouse parallax (the whole world follows) */
    rig.az += (rig.dAz - rig.az) * 0.07;
    rig.pol += (rig.dPol - rig.pol) * 0.07;
    rig.r += (rig.dR - rig.r) * 0.05;
    rig.tgt.lerp(rig.dTgt, 0.06);
    const pAz = rig.az + (rig.dragging ? 0 : rig.mx * 0.07 * FLIP * FLIP) ; /* idle drift removed: it jittered the CSS3D cards */
    const pPol = rig.pol + (rig.dragging ? 0 : rig.my * 0.045);
    camera.position.set(
        rig.tgt.x + rig.r * Math.sin(pPol) * Math.sin(pAz),
        rig.tgt.y + rig.r * Math.cos(pPol),
        rig.tgt.z + rig.r * Math.sin(pPol) * Math.cos(pAz)
    );
    camera.lookAt(rig.tgt);

    /* living platform */
    ringA.rotation.z += dt * (thinking ? 3.2 : 0.55);
    ringB.rotation.z -= dt * (thinking ? 2.4 : 0.33);
    discGlow.material.opacity = 0.42 + Math.sin(t * 2) * 0.1 + (thinking ? 0.25 : 0);
    particleGroup.rotation.y += dt * (thinking ? 0.14 : 0.045);
    halo.rotation.z += dt * 4;
    halo.material.opacity += ((thinking ? 0.85 : 0) - halo.material.opacity) * 0.1;

    /* avatar idle breath */
    if (avatarGroup) { avatarGroup.position.y = Math.sin(t * 0.9) * 0.012; }

    /* cards orbit gently in 3D and always face the viewer */
    if (!hoverPause && !panelOpen && !thinking) { orbitAngle += dt * 0.05; }
    cardsGroup.children.forEach((obj) => polarPlace(obj, Math.sin(orbitAngle) * 0.22));

    /* beam projects from the avatar toward the hologram side */
    if (beam.material.opacity > 0.01) {
        const src = new THREE.Vector3(0, 1.25, 0);
        const dirv = beamTarget.clone().sub(src);
        beam.position.copy(src);
        beam.scale.set(1, dirv.length(), 1);
        beam.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), dirv.normalize());
    }

    renderer.render(scene, camera);
    cssRenderer.render(scene, camera);
}
animate();

/* ============================ brain (demo — endpoint hook ready) ============================ */
function parseDates(text) {
    const now = new Date(), y = now.getFullYear(), m = now.getMonth();
    const lower = text.toLowerCase();
    const range = (a, b) => ({ from: a, to: b });
    const explicit = lower.match(/(\d{4}[-\/]\d{1,2}[-\/]\d{1,2}|\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/g);
    if (explicit && explicit.length >= 2) {
        const p = explicit.slice(0, 2).map((s) => {
            const seg = s.split(/[-\/]/).map(Number);
            return seg[0] > 999 ? new Date(seg[0], seg[1] - 1, seg[2]) : new Date(seg[2], seg[1] - 1, seg[0]);
        });
        if (p[0] > p[1]) { p.reverse(); }
        return range(p[0], p[1]);
    }
    if (/اليوم|today/.test(lower)) { return range(new Date(y, m, now.getDate()), now); }
    if (/أمس|امس|yesterday/.test(lower)) { return range(new Date(y, m, now.getDate() - 1), new Date(y, m, now.getDate() - 1, 23, 59)); }
    if (/هذا الشهر|الشهر الحالي|this month/.test(lower)) { return range(new Date(y, m, 1), now); }
    if (/الشهر الماضي|last month/.test(lower)) { return range(new Date(y, m - 1, 1), new Date(y, m, 0)); }
    if (/هذا العام|هذه السنة|السنة الحالية|this year/.test(lower)) { return range(new Date(y, 0, 1), now); }
    if (/العام الماضي|السنة الماضية|last year/.test(lower)) { return range(new Date(y - 1, 0, 1), new Date(y - 1, 11, 31)); }
    const q = lower.match(/الربع (الأول|الاول|الثاني|الثالث|الرابع)|q([1-4])|(first|second|third|fourth) quarter/);
    if (q) {
        const qi = q[2] ? (+q[2] - 1)
            : q[1] ? ({ 'الأول': 0, 'الاول': 0, 'الثاني': 1, 'الثالث': 2, 'الرابع': 3 })[q[1]]
            : ({ first: 0, second: 1, third: 2, fourth: 3 })[q[3]];
        return range(new Date(y, qi * 3, 1), new Date(y, qi * 3 + 3, 0));
    }
    let mi = -1;
    MONTHS_AR.concat(MONTHS_EN).forEach((name, i) => { if (lower.indexOf(name.toLowerCase()) !== -1) { mi = i % 12; } });
    const yr = lower.match(/\b(20\d{2})\b/);
    if (mi >= 0) { const yy = yr ? +yr[1] : y; return range(new Date(yy, mi, 1), new Date(yy, mi + 1, 0)); }
    if (yr) { return range(new Date(+yr[1], 0, 1), new Date(+yr[1], 11, 31)); }
    return null;
}
function sumRange(values, from, to) {
    let total = 0; const months = [];
    for (let i = 0; i < 12; i++) {
        const mStart = new Date(CP.year, i, 1), mEnd = new Date(CP.year, i + 1, 0);
        if (mEnd >= from && mStart <= to) { total += values[i]; months.push(i); }
    }
    return { total, months };
}
const QTR = CP.isAr ? ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'] : ['Q1', 'Q2', 'Q3', 'Q4'];
const sum = (arr) => arr.reduce((a, b) => a + b, 0);
function bestMonth(vals, monthIdx) {
    let bi = -1;
    monthIdx.forEach((i) => { if (vals[i] > 0 && (bi < 0 || vals[i] > vals[bi])) { bi = i; } });
    return bi >= 0 ? { label: MONTHS[bi], value: vals[bi] } : null;
}
function trendPct(vals, monthIdx) {
    const len = monthIdx.length;
    if (!len) { return null; }
    const prev = monthIdx.map((i) => i - len);
    if (prev.some((i) => i < 0)) { return null; }
    const pv = sum(prev.map((i) => vals[i]));
    if (pv <= 0) { return null; }
    return ((sum(monthIdx.map((i) => vals[i])) - pv) / pv) * 100;
}
function quarterValues(vals) {
    return [0, 1, 2, 3].map((q) => sum(vals.slice(q * 3, q * 3 + 3)));
}
function enrich(pills, text, vals, monthIdx, multiMonth) {
    const tp = trendPct(vals, monthIdx);
    if (tp !== null) { pills.push({ k: T['vs_prev'], v: (tp >= 0 ? '+' : '') + tp.toFixed(1) + '%' }); }
    if (multiMonth) {
        const bm = bestMonth(vals, monthIdx);
        if (bm) { text += ' ' + T['best_in'] + ' ' + bm.label + ' (' + fmt(bm.value) + ').'; }
    }
    return text;
}

const CopilotBrain = {
    ask(text) {
        /* Real AI endpoint (welcome/copilot_ask). It returns the exact same
         * shape localAnswer builds: { intent, icon, title, dates[], pills[],
         * text, chart[], link }. On ANY failure (network, timeout, non-200,
         * malformed) we fall back to the local demo brain, so the copilot
         * never breaks — zero regression. */
        const self = this;
        const ctrl = new AbortController();
        const timer = setTimeout(() => ctrl.abort(), 9000);
        const body = { q: text };
        <?php if ($this->security->get_csrf_hash()) { ?>body[<?= json_encode($this->security->get_csrf_token_name()) ?>] = <?= json_encode($this->security->get_csrf_hash()) ?>;<?php } ?>
        return fetch(CP.askEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
            signal: ctrl.signal,
            credentials: 'same-origin'
        })
            .then((r) => { if (!r.ok) { throw new Error('http ' + r.status); } return r.json(); })
            .then((res) => { if (!res || !res.intent) { throw new Error('bad shape'); } return res; })
            .catch(() => self.localAnswer(text))
            .finally(() => clearTimeout(timer));
    },
    localAnswer(text) {
        const lower = text.toLowerCase();
        const dates = parseDates(text);
        const inYear = dates && dates.from.getFullYear() === CP.year && dates.to.getFullYear() === CP.year;
        const series = (vals, label, color) => {
            const idx = inYear ? sumRange(vals, dates.from, dates.to).months : vals.map((_, i) => i);
            return { label, color, values: idx.map((i) => vals[i]), labels: idx.map((i) => MONTHS[i]) };
        };
        const rangeChips = () => dates
            ? [{ k: T['from'], v: fmtDate(dates.from) }, { k: T['to'], v: fmtDate(dates.to) }]
            : [{ k: T['from'], v: T['all_time'], solo: true }];

        if (/مبيعات|المبيعات|sales|revenue/.test(lower)) {
            const rr = inYear ? sumRange(CP.monthly.sales, dates.from, dates.to) : { total: CP.totals.sales.total, months: CP.monthly.sales.map((_, i) => i) };
            const pills = [{ k: T['sales'], v: fmt(rr.total) }, { k: T['received'], v: fmt(CP.totals.sales.paid) }, { k: T['due'], v: fmt(CP.totals.sales.due) }, { k: T['invoices'], v: String(CP.totals.sales.count) }];
            let text = T['total_is'] + ' ' + fmt(rr.total) + '.' + (inYear ? '' : ' ' + T['range_note'] + ' ' + CP.year + '.');
            text = enrich(pills, text, CP.monthly.sales, rr.months, rr.months.length > 1);
            return {
                intent: 'sales', icon: 'mdi mdi-finance', title: T['sales_t'], dates: rangeChips(),
                pills, text,
                chart: [series(CP.monthly.sales, T['sales'], '#818cf8')],
                link: CP.links.sales
            };
        }
        if (/مشتريات|المشتريات|purchase|expense/.test(lower)) {
            const rr = inYear ? sumRange(CP.monthly.purchases, dates.from, dates.to) : { total: CP.totals.purchases.total, months: CP.monthly.purchases.map((_, i) => i) };
            const pills = [{ k: T['purchases'], v: fmt(rr.total) }, { k: T['received'], v: fmt(CP.totals.purchases.paid) }, { k: T['due'], v: fmt(CP.totals.purchases.due) }, { k: T['invoices'], v: String(CP.totals.purchases.count) }];
            let text = T['total_is'] + ' ' + fmt(rr.total) + '.' + (inYear ? '' : ' ' + T['range_note'] + ' ' + CP.year + '.');
            text = enrich(pills, text, CP.monthly.purchases, rr.months, rr.months.length > 1);
            return {
                intent: 'purchases', icon: 'mdi mdi-chart-areaspline', title: T['purch_t'], dates: rangeChips(),
                pills, text,
                chart: [series(CP.monthly.purchases, T['purchases'], '#a78bfa')],
                link: CP.links.purchases
            };
        }
        if (/ضريبة|الضريبة|ضرائب|الضرائب|tax|vat/.test(lower)) {
            const to_ = inYear ? sumRange(CP.monthly.salesTax, dates.from, dates.to).total : CP.totals.tax.sales;
            const ti = inYear ? sumRange(CP.monthly.purchasesTax, dates.from, dates.to).total : CP.totals.tax.purchases;
            /* tax reads best per quarter — the VAT filing rhythm */
            const qOut = quarterValues(CP.monthly.salesTax), qIn = quarterValues(CP.monthly.purchasesTax);
            const qIdx = inYear ? [...new Set(sumRange(CP.monthly.salesTax, dates.from, dates.to).months.map((m) => Math.floor(m / 3)))] : [0, 1, 2, 3];
            return {
                intent: 'tax', icon: 'mdi mdi-percent', title: T['tax_t'], dates: rangeChips(),
                pills: [{ k: T['vat_out'], v: fmt(to_) }, { k: T['vat_in'], v: fmt(ti) }, { k: T['vat_net'], v: fmt(to_ - ti) }],
                text: T['vat_net'] + ': ' + fmt(to_ - ti) + '. (' + T['vat_out'] + ' ' + fmt(to_) + ' − ' + T['vat_in'] + ' ' + fmt(ti) + ')',
                chart: [
                    { label: T['vat_out'], color: '#818cf8', values: qIdx.map((q) => qOut[q]), labels: qIdx.map((q) => QTR[q]) },
                    { label: T['vat_in'], color: '#fbbf24', values: qIdx.map((q) => qIn[q]), labels: qIdx.map((q) => QTR[q]) }
                ],
                link: CP.links.tax
            };
        }
        if (/قارن|مقارنة|compare|vs/.test(lower)) {
            const cs = inYear ? sumRange(CP.monthly.sales, dates.from, dates.to).total : CP.totals.sales.total;
            const cpv = inYear ? sumRange(CP.monthly.purchases, dates.from, dates.to).total : CP.totals.purchases.total;
            const pills = [{ k: T['sales'], v: fmt(cs) }, { k: T['purchases'], v: fmt(cpv) }, { k: T['net_flow'], v: fmt(cs - cpv) }];
            if (cs > 0) { pills.push({ k: T['margin'], v: (((cs - cpv) / cs) * 100).toFixed(1) + '%' }); }
            return {
                intent: 'compare', icon: 'mdi mdi-swap-horizontal', title: T['cmp_t'], dates: rangeChips(),
                pills,
                text: T['net_flow'] + ': ' + fmt(cs - cpv) + (cs > 0 ? ' (' + T['margin'] + ' ' + (((cs - cpv) / cs) * 100).toFixed(1) + '%).' : '.'),
                chart: [series(CP.monthly.sales, T['sales'], '#818cf8'), series(CP.monthly.purchases, T['purchases'], '#a78bfa')],
                link: CP.links.sales
            };
        }
        /* Invoices are individual documents that only the server can hand out —
           the offline brain has no rows to show, so it says so plainly and
           still points at the real list instead of pretending not to understand. */
        if (/فاتور|فواتير|invoice|bill/.test(lower)) {
            return {
                intent: 'invoices', icon: 'mdi mdi-file-document-outline', title: T['inv_t'],
                dates: [], pills: [], rows: [], more: 0, text: T['inv_offline'],
                chart: null, link: CP.links.sales
            };
        }
        if (/مرحبا|أهلا|اهلا|سلام|hello|hi|help|مساعدة/.test(lower)) {
            return { intent: 'greet', icon: 'fa fa-diamond', title: T['greet_t'], dates: [], pills: [], text: T['greet'], chart: null, link: null };
        }
        return { intent: 'unknown', icon: 'mdi mdi-help-circle-outline', title: T['unknown_t'], dates: [], pills: [], text: T['unknown'], chart: null, link: null };
    }
};

/* ============================ panel render ============================ */
function barChart(seriesList) {
    if (!seriesList || !seriesList.length || !seriesList[0].values.length) { return ''; }
    const w = 600, h = 150, pad = 6, n = seriesList[0].values.length, groups = seriesList.length;
    let max = 1;
    seriesList.forEach((s) => s.values.forEach((v) => { if (v > max) { max = v; } }));
    const slot = (w - pad * 2) / n, bw = Math.min(26, Math.max(4, (slot - 6) / groups));
    let svg = '';
    seriesList.forEach((s, gi) => {
        s.values.forEach((v, i) => {
            const bh = Math.max(2, (v / max) * (h - 34));
            const x = pad + i * slot + (slot - bw * groups - (groups - 1) * 3) / 2 + gi * (bw + 3);
            svg += '<rect x="' + x.toFixed(1) + '" y="' + (h - 22 - bh).toFixed(1) + '" width="' + bw.toFixed(1) + '" height="' + bh.toFixed(1) +
                '" rx="3" fill="' + s.color + '" opacity="0.9"><title>' + s.label + ' · ' + (s.labels[i] || '') + ' · ' + fmtPlain(v) + '</title></rect>';
        });
    });
    seriesList[0].labels.forEach((lb, i) => {
        svg += '<text x="' + (pad + i * slot + slot / 2).toFixed(1) + '" y="' + (h - 6) + '" text-anchor="middle" font-size="9.5" fill="#a5abd4">' + lb.slice(0, 3) + '</text>';
    });
    const legend = seriesList.map((s) => '<span><i style="background:' + s.color + '"></i>' + s.label + '</span>').join('');
    return '<div class="cp-legend">' + legend + '</div><svg class="cp-bars" viewBox="0 0 ' + w + ' ' + h + '" preserveAspectRatio="none">' + svg + '</svg>';
}

/* Invoice rows. Only the money fields are markup (they can carry the Riyal
   glyph); every other value comes from the database and is escaped. */
function cpEsc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }
function invoiceRows(res) {
    let h = '<div class="cp-invs">';
    res.rows.forEach((r) => {
        const sub = [r.date, r.ref2, r.party].filter(Boolean).map(cpEsc).join(' · ');
        h += '<a class="cp-inv" href="' + cpEsc(r.url) + '" data-modal="' + cpEsc(r.modal) + '">' +
                 '<span class="cp-inv-main"><b>' + cpEsc(r.ref) + '</b><i class="cp-inv-sub">' + sub + '</i></span>' +
                 '<span class="cp-inv-side"><b>' + (r.total || '') + '</b>' +
                    (r.pay ? '<em class="cp-tag is-' + cpEsc(r.pay_key) + '">' + cpEsc(r.pay) + '</em>' : '') +
                 '</span></a>';
    });
    if (res.more > 0 && res.link) {
        h += '<a class="cp-more" href="' + cpEsc(res.link) + '">+' + cpEsc(String(res.more)) + ' ' + T['more'] + ' →</a>';
    }
    return h + '</div>';
}

let typeTimer = null, lastResponse = null, voiceOn = false;
const history = [];
function typewrite(el, text, done) {
    clearInterval(typeTimer);
    /* Tokenise so inline SVG/img (the Riyal symbol) is typed as one atomic unit —
       never sliced mid-tag. Plain text still types one character at a time. */
    const tokens = [];
    const re = /<svg\b[\s\S]*?<\/svg>|<img\b[^>]*>/gi;
    let last = 0, mm;
    while ((mm = re.exec(text)) !== null) {
        for (const ch of text.slice(last, mm.index)) { tokens.push(ch); }
        tokens.push(mm[0]);
        last = re.lastIndex;
    }
    for (const ch of text.slice(last)) { tokens.push(ch); }
    el.innerHTML = '<span class="cp-typed"></span><span class="cp-caret"></span>';
    const typed = el.firstChild, caret = el.lastChild;
    let i = 0, html = '';
    typeTimer = setInterval(() => {
        html += tokens[i++];
        typed.innerHTML = html;
        if (i >= tokens.length) { clearInterval(typeTimer); caret.remove(); if (done) { done(); } }
    }, 15);
}

function showResponse(res, silent) {
    lastResponse = res;
    panelEl.querySelector('#cpPanelTitle').innerHTML = '<i class="' + res.icon + '" style="color:#818cf8;margin-inline-end:8px"></i>' + res.title;
    panelEl.querySelector('#cpDates').innerHTML = (res.dates || []).map((d) =>
        '<span class="cp-date-chip">' + d.k + (d.solo ? '' : ':') + ' <b>' + d.v + '</b></span>').join('');
    panelEl.querySelector('#cpPills').innerHTML = (res.pills || []).map((p) =>
        '<span class="cp-pill">' + p.k + ' <b>' + p.v + '</b></span>').join('');
    panelEl.querySelector('#cpRows').innerHTML = (res.rows && res.rows.length) ? invoiceRows(res) : '';
    panelEl.querySelector('#cpChart').innerHTML = res.chart ? barChart(res.chart) : '';
    panelEl.querySelector('#cpFoot').innerHTML = res.link
        ? '<a class="cp-cta" href="' + res.link + '"><i class="fa fa-external-link"></i> ' + T['open_report'] + '</a>' : '';

    panelEl.classList.remove('materialize'); void panelEl.offsetWidth; panelEl.classList.add('materialize');
    panelOpen = true;
    cssHost.classList.add('cp-cards-dim');
    $('cpPanelHost').classList.add('on');
    if (!isMobile()) {
        beam.material.opacity = 0.5;
        addTween(0.5, 0, 1400, (v) => { beam.material.opacity = v; });
    }
    typewrite(panelEl.querySelector('#cpSay'), res.text, () => { if (voiceOn && !silent) { speak(res.title + '. ' + res.text); } });
}
function closePanel() {
    panelOpen = false;
    cssHost.classList.remove('cp-cards-dim');
    $('cpPanelHost').classList.remove('on');
    beam.material.opacity = 0;
    if (window.speechSynthesis) { speechSynthesis.cancel(); }
}
/* A row opens the invoice list's OWN modal (footer.php ships #myModal on every
   admin page), so the invoice arrives with its existing Print / PDF buttons.
   The href stays a real link, so new-tab and no-jQuery still work. */
panelEl.addEventListener('click', (e) => {
    const a = e.target.closest ? e.target.closest('.cp-inv') : null;
    if (!a || e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) { return; }
    const url = a.getAttribute('data-modal');
    if (!url || !window.jQuery || !jQuery('#myModal').length) { return; }
    e.preventDefault();
    closePanel();
    jQuery('#myModal').removeData('bs.modal').modal({ remote: url, show: true });
});
panelEl.querySelector('#cpClose').addEventListener('click', closePanel);
panelEl.querySelector('#cpSpeak').addEventListener('click', () => { if (lastResponse) { speak(lastResponse.title + '. ' + lastResponse.text); } });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closePanel(); } });

/* ---------- session ledger ---------- */
function dock(res) {
    if (res.intent === 'greet' || res.intent === 'unknown' || res.intent === 'chat') { return; }
    history.push(res);
    const ledger = $('cpLedger');
    const mini = document.createElement('span');
    mini.className = 'cp-mini';
    mini.innerHTML = '<i class="' + res.icon + '"></i>' + res.title +
        (res.dates && res.dates.length && !res.dates[0].solo ? ' · ' + res.dates[0].v : '');
    mini.addEventListener('click', () => showResponse(res, true));
    ledger.prepend(mini);
    ledger.classList.add('has');
    while (ledger.children.length > 8) { ledger.lastChild.remove(); }
}

/* ---------- voice ---------- */
function speak(text) {
    if (!window.speechSynthesis) { return; }
    speechSynthesis.cancel();
    const u = new SpeechSynthesisUtterance(text.replace(/<[^>]+>/g, ''));
    u.lang = CP.isAr ? 'ar-SA' : 'en-US';
    speechSynthesis.speak(u);
}
$('cpVoice').addEventListener('click', function () {
    voiceOn = !voiceOn;
    this.classList.toggle('on', voiceOn);
    this.title = voiceOn ? T['voice_on'] : T['voice_off'];
    this.firstElementChild.className = voiceOn ? 'fa fa-volume-up' : 'fa fa-volume-off';
    if (!voiceOn && window.speechSynthesis) { speechSynthesis.cancel(); }
});

/* ---------- ask flow (avatar focus + halo while thinking) ---------- */
let busy = false;
function ask(text) {
    if (busy || !text.trim()) { return; }
    busy = true;
    closePanel();
    thinking = true;
    $('cpWave').classList.add('on');
    $('cpCore').classList.add('think');
    $('cpSend').disabled = true;
    $('cpInput').setAttribute('placeholder', T['thinking']);
    rig.dR = baseRadius() * 0.82; rig.dTgt.y = 1.22; /* the camera leans in while he thinks */

    CopilotBrain.ask(text).then((res) => {
        showResponse(res);
        dock(res);
    }).catch((err) => {
        if (window.console) { console.error('copilot ask failed:', err); }
    }).finally(() => {
        /* the deck must always return to a usable state, even on errors */
        thinking = false;
        $('cpWave').classList.remove('on');
        $('cpCore').classList.remove('think');
        $('cpSend').disabled = false;
        $('cpInput').setAttribute('placeholder', <?= json_encode($t['placeholder'], JSON_UNESCAPED_UNICODE) ?>);
        rig.dR = baseRadius(); rig.dTgt.y = 1.02;
        busy = false;
    });
}
/* plain div + button: the host app's global form handlers can't interfere */
function submitAsk() {
    const v = $('cpInput').value;
    $('cpInput').value = '';
    ask(v);
}
$('cpSend').addEventListener('click', submitAsk);
$('cpInput').addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); submitAsk(); } });
$('cpChips').addEventListener('click', (e) => {
    const chip = e.target.closest('.cp-chip');
    if (chip) { ask(chip.textContent); }
});

/* ---------- clock + boot ---------- */
(function clockTick() {
    const el = $('cpClock');
    const tick = () => {
        const d = new Date();
        el.textContent = String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    };
    tick(); setInterval(tick, 15000);
})();

let bootedUI = false;
function bootUI() {
    if (bootedUI) { return; }
    bootedUI = true;
    CP.booted = true;
    $('cpLoader').classList.add('off');
    cardsGroup.children.forEach((obj, i) => {
        setTimeout(() => addTween(0.0001, CARD_SCALE, 700, (v) => obj.scale.setScalar(v)), 250 + i * 220);
    });
    setTimeout(animateCounters, 500);
    setTimeout(() => showResponse(CopilotBrain.localAnswer(CP.isAr ? 'مرحبا' : 'hello'), true), 1400);
    /* draw the eye to the command capsule */
    const capsule = document.querySelector('.cp-capsule');
    capsule.classList.add('attn');
    $('cpInput').addEventListener('focus', () => capsule.classList.remove('attn'), { once: true });
    if (!isMobile()) { setTimeout(() => $('cpInput').focus({ preventScroll: true }), 2200); }
}
</script>

<script>
/* Voice input — Chrome/Edge Web Speech API (built in, free, no extension). */
(function () {
    var mic = document.getElementById('cpMic'), inp = document.getElementById('cpInput'),
        send = document.getElementById('cpSend'), AR = <?= $is_ar ? 'true' : 'false' ?>;
    if (!mic) return;
    var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SR) { mic.style.display = 'none'; return; }   // unsupported browser: stay silent

    var rec = new SR(), on = false;
    rec.lang = AR ? 'ar-SA' : 'en-US';
    rec.interimResults = true;
    rec.continuous = false;

    rec.onstart  = function () { on = true;  mic.classList.add('rec'); };
    rec.onend    = function () { on = false; mic.classList.remove('rec'); };
    rec.onerror  = function () { on = false; mic.classList.remove('rec'); };
    rec.onresult = function (e) {
        var txt = '', done = false;
        for (var i = e.resultIndex; i < e.results.length; i++) {
            txt += e.results[i][0].transcript;
            if (e.results[i].isFinal) done = true;
        }
        inp.value = txt;
        if (done && txt.trim()) { try { rec.stop(); } catch (x) {} send.click(); }
    };
    mic.addEventListener('click', function () {
        if (on) { try { rec.stop(); } catch (x) {} return; }
        try { rec.start(); } catch (x) {}
    });
})();
</script>
