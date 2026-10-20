<?php
defined('BASEPATH') or exit('No direct script access allowed');

$is_arabic = (isset($Settings->user_language) && $Settings->user_language == 'arabic');
$placeholder = $is_arabic ? 'ابحث عن صفحة أو تقرير أو إعداد... (Ctrl + K)' : 'Search pages, reports or settings... (Ctrl + K)';
$modal_aria_label = $is_arabic ? 'مركز البحث والتنقل السريع' : 'Global Command Palette';
?>
<!-- Spark Global Command Center / Search Palette Modal -->
<div class="spark-search-backdrop spark-command-palette" id="sparkCommandPalette" role="presentation" aria-hidden="true">
  <div class="spark-search-modal" role="dialog" aria-modal="true" aria-label="<?= htmlspecialchars($modal_aria_label, ENT_QUOTES, 'UTF-8'); ?>">
    
    <!-- Modal Search Header -->
    <div class="spark-search-header">
      <div class="spark-search-icon-wrap" aria-hidden="true">
        <i class="fa fa-search"></i>
      </div>
      
      <input
        type="text"
        id="sparkGlobalSearchInput"
        class="spark-search-input"
        placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'); ?>"
        autocomplete="off"
        autocorrect="off"
        autocapitalize="off"
        spellcheck="false"
        role="combobox"
        aria-expanded="false"
        aria-controls="sparkSearchResultsList"
        aria-autocomplete="list"
      />
      
      <button type="button" id="sparkSearchClear" class="spark-search-clear" aria-label="<?= $is_arabic ? 'مسح البحث' : 'Clear search'; ?>" title="<?= $is_arabic ? 'مسح' : 'Clear'; ?>">
        <i class="fa fa-times"></i>
      </button>

      <button type="button" class="spark-search-close-btn" id="sparkSearchClose" aria-label="<?= $is_arabic ? 'إغلاق (Esc)' : 'Close (Esc)'; ?>" title="<?= $is_arabic ? 'إغلاق' : 'Close'; ?>">
        ESC
      </button>
    </div>

    <!-- Modal Results Body -->
    <div class="spark-search-body" id="sparkSearchResults" role="region" aria-live="polite">
      <!-- Dynamic results or recent items populated by JavaScript -->
    </div>

    <!-- Modal Footer / Keyboard Hints -->
    <div class="spark-search-footer">
      <div class="spark-search-hints">
        <span class="spark-search-hint">
          <kbd class="spark-search-kbd">↑</kbd>
          <kbd class="spark-search-kbd">↓</kbd>
          <span><?= $is_arabic ? 'للتنقل' : 'to navigate'; ?></span>
        </span>
        <span class="spark-search-hint">
          <kbd class="spark-search-kbd">↵</kbd>
          <span><?= $is_arabic ? 'للاختيار' : 'to select'; ?></span>
        </span>
        <span class="spark-search-hint">
          <kbd class="spark-search-kbd">ESC</kbd>
          <span><?= $is_arabic ? 'للإغلاق' : 'to close'; ?></span>
        </span>
      </div>
      
      <div class="spark-search-stats" id="sparkSearchStats">
        <!-- Results count will appear here -->
      </div>
    </div>

  </div>
</div>
