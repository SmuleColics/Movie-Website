document.addEventListener('DOMContentLoaded', function () {
  // Attach event listeners to all volume-control buttons inside modals
  document.querySelectorAll('.volume-control').forEach(function (volumeBtn) {
    volumeBtn.addEventListener('click', function () {
      const modal = volumeBtn.closest('.modal');
      const video = modal ? modal.querySelector('.video-player') : null;
      const volumeIcon = volumeBtn.querySelector('.volume-icon');
      if (video && volumeIcon) {
        video.muted = !video.muted;
        if (video.muted) {
          volumeIcon.classList.remove('fa-volume-high');
          volumeIcon.classList.add('fa-volume-xmark');
        } else {
          volumeIcon.classList.remove('fa-volume-xmark');
          volumeIcon.classList.add('fa-volume-high');
        }
      }
    });
  });

  // Optional: When modal is shown, sync the icon to the video state (Bootstrap 5 event)
  document.querySelectorAll('.modal').forEach(function (modal) {
    modal.addEventListener('shown.bs.modal', function () {
      const video = modal.querySelector('.video-player');
      const volumeBtn = modal.querySelector('.volume-control');
      const volumeIcon = volumeBtn ? volumeBtn.querySelector('.volume-icon') : null;
      if (video && volumeIcon) {
        if (video.muted) {
          volumeIcon.classList.remove('fa-volume-high');
          volumeIcon.classList.add('fa-volume-xmark');
        } else {
          volumeIcon.classList.remove('fa-volume-xmark');
          volumeIcon.classList.add('fa-volume-high');
        }
      }
    });
  });
});