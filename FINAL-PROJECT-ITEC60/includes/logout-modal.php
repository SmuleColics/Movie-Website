<?php
/*
 * "Sign out?" confirmation popup, shared by the member and admin headers.
 * $cvLogoutAction is the URL of the sign-out page, relative to the current page.
 * Any link with data-bs-toggle="modal" data-bs-target="#cvLogoutModal" opens it.
 */
$cvLogoutAction = $cvLogoutAction ?? 'logout.php';
$cvLogoutMessage = $cvLogoutMessage ?? 'You will need to sign in again to keep watching.';
?>
<div class="modal fade" id="cvLogoutModal" tabindex="-1" aria-labelledby="cvLogoutTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content text-white" style="background:#221C15; border:1px solid #3D3326; border-radius:14px;">
      <div class="modal-body text-center p-4">
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
          style="width:64px; height:64px; border-radius:50%; background:rgba(201, 162, 75,.18);">
          <i class="fa-solid fa-right-from-bracket" style="font-size:26px; color:#C9A24B;"></i>
        </div>
        <h2 class="fs-4 fw-semibold mb-2" id="cvLogoutTitle">Sign out?</h2>
        <p class="mb-4" style="color:#D8CCB0;"><?php echo e($cvLogoutMessage); ?></p>
        <form method="post" action="<?php echo e($cvLogoutAction); ?>" class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn text-white px-4" style="background:#C9A24B;">Sign out</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  // The headers are fixed bars with their own stacking layer, which would trap
  // this popup under Bootstrap's dark backdrop. Move it to <body> so it sits on top.
  document.addEventListener('DOMContentLoaded', function () {
    var m = document.getElementById('cvLogoutModal');
    if (m && m.parentElement !== document.body) document.body.appendChild(m);
  });
</script>
