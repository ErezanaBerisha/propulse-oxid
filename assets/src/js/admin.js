(function () {
  function markCopied(button) {
    var original = button.textContent;
    var mount = document.getElementById('propulse-qr');
    var copied = (mount && mount.getAttribute('data-copied')) || 'Copied';
    button.textContent = copied;
    window.setTimeout(function () {
      button.textContent = original;
    }, 1500);
  }

  function copyText(value, button) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(value).then(function () {
        markCopied(button);
      });
      return;
    }

    var field = document.createElement('textarea');
    field.value = value;
    document.body.appendChild(field);
    field.select();
    document.execCommand('copy');
    document.body.removeChild(field);
    markCopied(button);
  }

  document.querySelectorAll('.propulse-copy').forEach(function (button) {
    button.addEventListener('click', function () {
      var id = button.getAttribute('data-target');
      var input = id ? document.getElementById(id) : null;
      if (!input || !input.value) {
        return;
      }
      copyText(input.value, button);
    });
  });

  var mount = document.getElementById('propulse-qr');
  if (!mount || typeof window.propulseQrcode !== 'function') {
    return;
  }

  var email = mount.getAttribute('data-email') || '';
  var password = mount.getAttribute('data-password') || '';
  var shopUrl = mount.getAttribute('data-shop-url') || '';
  var platformType = mount.getAttribute('data-platform-type') || '5';

  if (!email || !password || !shopUrl) {
    return;
  }

  var payload = [email, password, shopUrl, String(platformType)].join('|');
  var code = window.propulseQrcode(0, 'M');
  code.addData(payload);
  code.make();
  mount.innerHTML = code.createSvgTag({ cellSize: 4, margin: 8 });
})();
