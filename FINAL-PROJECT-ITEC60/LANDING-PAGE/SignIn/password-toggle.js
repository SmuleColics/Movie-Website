// Adds a show/hide eye button to every password field on the page.
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('input[type="password"]').forEach(function (input) {
    var holder = input.parentElement;
    if (getComputedStyle(holder).position === 'static') {
      holder.style.position = 'relative';
    }

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'cv-eye-btn';
    btn.setAttribute('aria-label', 'Show password');
    btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
    // Inline styles so the button also looks right on pages without SignIn.css (admin sign in).
    Object.assign(btn.style, {
      position: 'absolute', right: '12px', transform: 'translateY(-50%)',
      background: 'transparent', border: '0', padding: '4px 6px',
      color: '#c9d1d9', fontSize: '16px', lineHeight: '1', cursor: 'pointer', zIndex: '2'
    });
    holder.appendChild(btn);

    input.style.paddingRight = '44px';

    // Centre the eye on the input itself (error text below must not shift it).
    function place() {
      btn.style.top = (input.offsetTop + input.offsetHeight / 2) + 'px';
    }
    place();
    window.addEventListener('resize', place);

    btn.addEventListener('click', function () {
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.innerHTML = show ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      input.focus();
    });
  });
});
