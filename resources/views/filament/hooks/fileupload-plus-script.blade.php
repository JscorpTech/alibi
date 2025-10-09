<script>
(() => {
  const SELECTOR = '[data-plus="1"] .filepond--drop-label label';
  const BTN = `
    <span class="filepond-plus-button" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none">
        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </span>`;

  function apply(root = document) {
    root.querySelectorAll(SELECTOR).forEach((label) => {
      if (label.dataset.plusApplied) return;
      label.dataset.plusApplied = '1';
      label.innerHTML = BTN;
    });
  }

  apply();
  new MutationObserver(() => apply()).observe(document.body, { childList: true, subtree: true });
})();
</script>