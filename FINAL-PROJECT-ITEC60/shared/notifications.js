// Notification bell, shared by the admin and member top bars.
// The menu (.cv-notif-menu) says where to post via data-notif-endpoint;
// each item (.js-notif-item) carries its id in data-notif-id.
// Red dot = unread. "Mark all as read" and opening an item mark them read.
(function () {
  const menu = document.querySelector('.cv-notif-menu[data-notif-endpoint]');
  if (!menu) return;

  const endpoint = menu.dataset.notifEndpoint;
  const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const badge = document.getElementById('cv-bell-badge');
  const readAll = document.getElementById('cv-notif-read-all');

  function send(data) {
    return fetch(endpoint, {
      method: 'POST',
      headers: { 'X-CSRF-Token': token },
      body: new URLSearchParams(data),
      keepalive: true
    }).then(r => r.json()).catch(() => null);
  }

  function setCount(n) {
    if (badge) {
      badge.textContent = n > 99 ? '99+' : n;
      badge.hidden = n <= 0;
    }
    if (readAll) readAll.hidden = n <= 0;
  }

  if (readAll) {
    readAll.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation(); // keep the menu open
      send({ action: 'read_all' }).then(res => {
        menu.querySelectorAll('.js-notif-item.unread').forEach(el => el.classList.remove('unread'));
        setCount(res && typeof res.unread === 'number' ? res.unread : 0);
      });
    });
  }

  menu.querySelectorAll('.js-notif-item').forEach(function (item) {
    item.addEventListener('click', function (e) {
      if (!item.classList.contains('unread')) return; // already read: just follow the link
      e.preventDefault();
      item.classList.remove('unread');
      send({ action: 'read_one', id: item.dataset.notifId })
        .finally(() => { window.location.href = item.href; });
    });
  });
})();
