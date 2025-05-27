document.addEventListener("DOMContentLoaded", function () {
  // --- Trending Slider Logic ---
  const trendingSlider = document.querySelector(".trending-images-container");
  const trendingNextButton = document.querySelector(".next-chevron-btn-trending");
  const trendingPrevButton = document.querySelector(".prev-chevron-btn-trending");
  let trendingScrollAmount = 0;
  let trendingScrollStep = 500;

  trendingSlider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";

  function getTrendingMaxScroll() {
    return trendingSlider.scrollWidth - trendingSlider.clientWidth;
  }

  function updateTrendingButtons() {
    if (trendingScrollAmount <= 0) {
      trendingPrevButton.closest(".prev-button-trending").style.display = "none";
    } else {
      trendingPrevButton.closest(".prev-button-trending").style.display = "";
    }
    if (
      getTrendingMaxScroll() <= 0 ||
      trendingScrollAmount >= getTrendingMaxScroll() - 5
    ) {
      trendingNextButton.closest(".next-button-trending").style.display = "none";
    } else {
      trendingNextButton.closest(".next-button-trending").style.display = "";
    }
  }

  trendingNextButton.addEventListener("click", function () {
    trendingScrollAmount += trendingScrollStep;
    if (trendingScrollAmount > getTrendingMaxScroll())
      trendingScrollAmount = getTrendingMaxScroll();
    trendingSlider.style.transform = `translateX(-${trendingScrollAmount}px)`;
    updateTrendingButtons();
  });
  trendingPrevButton.addEventListener("click", function () {
    trendingScrollAmount -= trendingScrollStep;
    if (trendingScrollAmount < 0) trendingScrollAmount = 0;
    trendingSlider.style.transform = `translateX(-${trendingScrollAmount}px)`;
    updateTrendingButtons();
  });
  window.addEventListener("resize", function () {
    if (trendingScrollAmount > getTrendingMaxScroll()) {
      trendingScrollAmount = getTrendingMaxScroll();
      trendingSlider.style.transform = `translateX(-${trendingScrollAmount}px)`;
    }
    updateTrendingButtons();
  });

  // Initial state
  updateTrendingButtons();

  // --- Top 10 Slider Logic ---
  const top10Slider = document.querySelector(".top-10-images-container");
  const top10NextButton = document.querySelector(".next-chevron-btn-top10");
  const top10PrevButton = document.querySelector(".prev-chevron-btn-top10");
  let top10ScrollAmount = 0;
  let top10ScrollStep = 500;
  top10Slider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";

  function getTop10MaxScroll() {
    return top10Slider.scrollWidth - top10Slider.clientWidth;
  }
  function updateTop10Buttons() {
    if (top10ScrollAmount <= 0) {
      top10PrevButton.closest(".prev-button-top10").style.display = "none";
    } else {
      top10PrevButton.closest(".prev-button-top10").style.display = "";
    }
    if (
      getTop10MaxScroll() <= 0 ||
      top10ScrollAmount >= getTop10MaxScroll() - 5
    ) {
      top10NextButton.closest(".next-button-top10").style.display = "none";
    } else {
      top10NextButton.closest(".next-button-top10").style.display = "";
    }
  }
  top10NextButton.addEventListener("click", function () {
    top10ScrollAmount += top10ScrollStep;
    if (top10ScrollAmount > getTop10MaxScroll())
      top10ScrollAmount = getTop10MaxScroll();
    top10Slider.style.transform = `translateX(-${top10ScrollAmount}px)`;
    updateTop10Buttons();
  });
  top10PrevButton.addEventListener("click", function () {
    top10ScrollAmount -= top10ScrollStep;
    if (top10ScrollAmount < 0) top10ScrollAmount = 0;
    top10Slider.style.transform = `translateX(-${top10ScrollAmount}px)`;
    updateTop10Buttons();
  });
  window.addEventListener("resize", function () {
    if (top10ScrollAmount > getTop10MaxScroll()) {
      top10ScrollAmount = getTop10MaxScroll();
      top10Slider.style.transform = `translateX(-${top10ScrollAmount}px)`;
    }
    updateTop10Buttons();
  });
  updateTop10Buttons();

  // Set video start time for all videos with class 'video-episode'
  const videos = document.getElementsByClassName("video-episode");
  Array.from(videos).forEach(function (video) {
    video.addEventListener("loadedmetadata", function () {
      video.currentTime = 5;
    });
  });
});
