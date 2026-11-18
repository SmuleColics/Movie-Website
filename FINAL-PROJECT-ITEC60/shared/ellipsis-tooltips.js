// Shows the full text in a tooltip on any text the browser has cut short with "...".
// Works for one-line ellipsis (text-overflow: ellipsis) and multi-line clamps
// (-webkit-line-clamp). Text that already fits gets no tooltip.
// Loaded on the member, admin and landing pages.
(function () {
  if (!window.bootstrap || !bootstrap.Tooltip) return;

  function isTruncated(el) {
    const cs = getComputedStyle(el);
    const oneLine = cs.textOverflow === 'ellipsis' && el.scrollWidth > el.clientWidth + 1;
    const clamped = cs.webkitLineClamp && cs.webkitLineClamp !== 'none' && el.scrollHeight > el.clientHeight + 1;
    return oneLine || clamped;
  }

  function findTruncated(start) {
    let el = start;
    for (let i = 0; el && el !== document.body && i < 4; i++, el = el.parentElement) {
      if (el.nodeType === 1 && isTruncated(el)) return el;
    }
    return null;
  }

  function handle(e) {
    const el = findTruncated(e.target);
    if (!el || el.hasAttribute('data-bs-toggle')) return; // elements with their own tooltip keep it
    const text = el.textContent.replace(/\s+/g, ' ').trim();
    if (!text) return;

    let tip = bootstrap.Tooltip.getInstance(el);
    if (!tip) {
      tip = new bootstrap.Tooltip(el, {
        title: text,
        placement: 'top',
        trigger: 'hover focus',
        customClass: 'cv-ellipsis-tooltip',
        container: 'body'
      });
    } else {
      tip.setContent({ '.tooltip-inner': text });
    }
    tip.show();
  }

  document.addEventListener('mouseover', handle);
  document.addEventListener('focusin', handle);

  // Wide, left-aligned tooltip for long text. The text color is set on
  // purpose: Bootstrap's dark mode makes tooltip text the page background
  // color, which left dark text on a dark box. z-index keeps it above the
  // fixed top bars.
  const style = document.createElement('style');
  style.textContent =
    '.cv-ellipsis-tooltip{--bs-tooltip-max-width:420px;--bs-tooltip-bg:#2A231A;--bs-tooltip-color:#F1E8D4;--bs-tooltip-opacity:1;--bs-tooltip-font-size:13px;z-index:2100;}' +
    '.cv-ellipsis-tooltip .tooltip-inner{color:#F1E8D4;background:#2A231A;text-align:left;line-height:1.45;border:1px solid #3D3326;padding:8px 12px;}';
  document.head.appendChild(style);
})();
