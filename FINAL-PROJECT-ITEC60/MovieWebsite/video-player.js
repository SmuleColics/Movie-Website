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
      // Previews load only when a title is opened, so a page full of titles
      // doesn't start streaming every movie at once.
      const preview = modal.querySelector("video[data-cv-autoplay]");
      // Settings > Playback can turn previews off.
      const previewsOn = !(window.CV_PREFS && window.CV_PREFS.autoplay_previews === 0);
      if (preview && previewsOn) {
        if (!preview.dataset.cvStarted) {
          preview.dataset.cvStarted = "1";
          preview.preload = "auto";
          // Long films: skip the opening credits and show a scene.
          preview.addEventListener("loadedmetadata", function () {
            if (preview.duration > 1200) preview.currentTime = Math.min(300, preview.duration * 0.15);
          }, { once: true });
          preview.load();
        }
        preview.play().catch(function () {});
      }
      modal.querySelectorAll("video.video-episode").forEach(function (ep) {
        if (ep.preload === "none") {
          ep.preload = "metadata";
          ep.addEventListener("loadedmetadata", function () {
            if (ep.duration > 120) ep.currentTime = Math.min(90, ep.duration * 0.1); // a frame, not the black intro
          }, { once: true });
          ep.load();
        }
      });

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
      const preview = modal.querySelector("video[data-cv-autoplay]");
      if (preview) preview.pause();

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
