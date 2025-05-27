document.addEventListener("DOMContentLoaded", function () {
  // Attach event listeners to all volume-control buttons inside modals
  document.querySelectorAll(".volume-control").forEach(function (volumeBtn) {
    volumeBtn.addEventListener("click", function () {
      const modal = volumeBtn.closest(".modal");
      const video = modal ? modal.querySelector(".video-player") : null;
      const volumeIcon = volumeBtn.querySelector(".volume-icon");
      if (video && volumeIcon) {
        video.muted = !video.muted;
        if (video.muted) {
          volumeIcon.classList.remove("fa-volume-high");
          volumeIcon.classList.add("fa-volume-xmark");
        } else {
          volumeIcon.classList.remove("fa-volume-xmark");
          volumeIcon.classList.add("fa-volume-high");
        }
      }
    });
  });

  // Attach shown/hidden events to all modals
  document.querySelectorAll(".modal").forEach(function (modal) {
    // When modal is shown
    modal.addEventListener("shown.bs.modal", function () {
      //console.log('Modal shown');
      const video = modal.querySelector(".video-player");
      const volumeBtn = modal.querySelector(".volume-control");
      const volumeIcon = volumeBtn
        ? volumeBtn.querySelector(".volume-icon")
        : null;
      if (video && volumeIcon) {
        if (video.muted) {
          volumeIcon.classList.remove("fa-volume-high");
          volumeIcon.classList.add("fa-volume-xmark");
        } else {
          volumeIcon.classList.remove("fa-volume-xmark");
          volumeIcon.classList.add("fa-volume-high");
        }
      }
    });
    // When modal is hidden
    modal.addEventListener("hidden.bs.modal", function () {
      const video = modal.querySelector(".video-player");
      // Mute the video no matter what
      if (video) video.muted = true;

      // Always set the icon to muted state, if it exists
      const volumeBtn = modal.querySelector(".volume-control");
      if (volumeBtn) {
        const volumeIcon = volumeBtn.querySelector(".volume-icon");
        if (volumeIcon) {
          volumeIcon.classList.remove("fa-volume-high");
          volumeIcon.classList.add("fa-volume-xmark");
        }
      }
    });
  });
});
