document.addEventListener("DOMContentLoaded", function () {
  const dramaSlider = document.querySelector("#section-drama .action-images-container");
  const dramaNextButton = document.querySelector(".next-chevron-btn-drama");
  const dramaPrevButton = document.querySelector(".prev-chevron-btn-drama");
  let dramaScrollAmount = 0;
  let dramaScrollStep = 500;

  function getDramaMaxScroll() {
    return dramaSlider.scrollWidth - dramaSlider.clientWidth;
  }
  function updateDramaButtons() {
    if (dramaScrollAmount <= 0) {
      dramaPrevButton.closest(".prev-button-drama").style.display = "none";
    } else {
      dramaPrevButton.closest(".prev-button-drama").style.display = "";
    }
    if (getDramaMaxScroll() <= 0 || dramaScrollAmount >= getDramaMaxScroll() - 5) {
      dramaNextButton.closest(".next-button-drama").style.display = "none";
    } else {
      dramaNextButton.closest(".next-button-drama").style.display = "";
    }
  }

  if (dramaSlider && dramaNextButton && dramaPrevButton) {
    dramaSlider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
    dramaNextButton.addEventListener("click", function () {
      dramaScrollAmount += dramaScrollStep;
      if (dramaScrollAmount > getDramaMaxScroll())
        dramaScrollAmount = getDramaMaxScroll();
      dramaSlider.style.transform = `translateX(-${dramaScrollAmount}px)`;
      updateDramaButtons();
    });
    dramaPrevButton.addEventListener("click", function () {
      dramaScrollAmount -= dramaScrollStep;
      if (dramaScrollAmount < 0) dramaScrollAmount = 0;
      dramaSlider.style.transform = `translateX(-${dramaScrollAmount}px)`;
      updateDramaButtons();
    });
    window.addEventListener("resize", function () {
      if (dramaScrollAmount > getDramaMaxScroll()) {
        dramaScrollAmount = getDramaMaxScroll();
        dramaSlider.style.transform = `translateX(-${dramaScrollAmount}px)`;
      }
      updateDramaButtons();
    });
    updateDramaButtons();
  }
  // --- Recommended Slider Logic ---
  const recSlider = document.querySelector(".popular-images-container");
  const recNextButton = document.querySelector(".next-chevron-btn-recommended");
  const recPrevButton = document.querySelector(".prev-chevron-btn-recommended");
  let recScrollAmount = 0;
  let recScrollStep = 500;

  function getRecMaxScroll() {
    return recSlider.scrollWidth - recSlider.clientWidth;
  }
  function updateRecButtons() {
    // Debug info
    // console.log("recScrollAmount:", recScrollAmount, "maxScroll:", getRecMaxScroll());
    if (recScrollAmount <= 0) {
      recPrevButton.closest(".prev-button-recommended").style.display = "none";
    } else {
      recPrevButton.closest(".prev-button-recommended").style.display = "";
    }
    if (getRecMaxScroll() <= 0 || recScrollAmount >= getRecMaxScroll() - 5) {
      recNextButton.closest(".next-button-recommended").style.display = "none";
    } else {
      recNextButton.closest(".next-button-recommended").style.display = "";
    }
  }

  if (recSlider && recNextButton && recPrevButton) {
    recSlider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
    recNextButton.addEventListener("click", function () {
      recScrollAmount += recScrollStep;
      if (recScrollAmount > getRecMaxScroll())
        recScrollAmount = getRecMaxScroll();
      recSlider.style.transform = `translateX(-${recScrollAmount}px)`;
      updateRecButtons();
    });
    recPrevButton.addEventListener("click", function () {
      recScrollAmount -= recScrollStep;
      if (recScrollAmount < 0) recScrollAmount = 0;
      recSlider.style.transform = `translateX(-${recScrollAmount}px)`;
      updateRecButtons();
    });
    window.addEventListener("resize", function () {
      if (recScrollAmount > getRecMaxScroll()) {
        recScrollAmount = getRecMaxScroll();
        recSlider.style.transform = `translateX(-${recScrollAmount}px)`;
      }
      updateRecButtons();
    }); 
    updateRecButtons(); // Call at start
  }

  // Cookie logic: Save genre on click for future recommendations
  document.querySelectorAll('.recommended-link').forEach(function(link) {
    link.addEventListener('click', function() {
      let genre = this.getAttribute('data-genre1') || this.getAttribute('data-genre2') || this.getAttribute('data-genre3');
      if (genre) {
        document.cookie = "recommended_genre=" + encodeURIComponent(genre) + "; path=/; max-age=" + (60 * 60 * 24 * 7);
      }
    });
  });

  // --- Featured/ACTION Slider Logic ---
  const actionSlider = document.querySelector(".featured-images-container");
  const actionNextButton = document.querySelector(".next-chevron-btn-featured");
  const actionPrevButton = document.querySelector(".prev-chevron-btn-featured");
  let actionScrollAmount = 0;
  let actionScrollStep = 500;
  if (actionSlider && actionNextButton && actionPrevButton) {
    actionSlider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
    function getActionMaxScroll() { return actionSlider.scrollWidth - actionSlider.clientWidth; }
    function updateActionButtons() {
      if (actionScrollAmount <= 0) {
        actionPrevButton.closest(".prev-button-featured").style.display = "none";
      } else {
        actionPrevButton.closest(".prev-button-featured").style.display = "";
      }
      if (getActionMaxScroll() <= 0 || actionScrollAmount >= getActionMaxScroll() - 5) {
        actionNextButton.closest(".next-button-featured").style.display = "none";
      } else {
        actionNextButton.closest(".next-button-featured").style.display = "";
      }
    }
    actionNextButton.addEventListener("click", function () {
      actionScrollAmount += actionScrollStep;
      if (actionScrollAmount > getActionMaxScroll())
        actionScrollAmount = getActionMaxScroll();
      actionSlider.style.transform = `translateX(-${actionScrollAmount}px)`;
      updateActionButtons();
    });
    actionPrevButton.addEventListener("click", function () {
      actionScrollAmount -= actionScrollStep;
      if (actionScrollAmount < 0) actionScrollAmount = 0;
      actionSlider.style.transform = `translateX(-${actionScrollAmount}px)`;
      updateActionButtons();
    });
    window.addEventListener("resize", function () {
      if (actionScrollAmount > getActionMaxScroll()) {
        actionScrollAmount = getActionMaxScroll();
        actionSlider.style.transform = `translateX(-${actionScrollAmount}px)`;
      }
      updateActionButtons();
    });
    updateActionButtons();
  }

  // --- Trending Slider Logic ---
  const trendingSlider = document.querySelector(".trending-images-container");
  const trendingNextButton = document.querySelector(".next-chevron-btn-trending");
  const trendingPrevButton = document.querySelector(".prev-chevron-btn-trending");
  let trendingScrollAmount = 0;
  let trendingScrollStep = 500;
  if (trendingSlider && trendingNextButton && trendingPrevButton) {
    trendingSlider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
    function getTrendingMaxScroll() { return trendingSlider.scrollWidth - trendingSlider.clientWidth; }
    function updateTrendingButtons() {
      if (trendingScrollAmount <= 0) {
        trendingPrevButton.closest(".prev-button-trending").style.display = "none";
      } else {
        trendingPrevButton.closest(".prev-button-trending").style.display = "";
      }
      if (getTrendingMaxScroll() <= 0 || trendingScrollAmount >= getTrendingMaxScroll() - 5) {
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
    updateTrendingButtons();
  }

  // --- Top 10 Slider Logic ---
  const top10Slider = document.querySelector(".top-10-images-container");
  const top10NextButton = document.querySelector(".next-chevron-btn-top10");
  const top10PrevButton = document.querySelector(".prev-chevron-btn-top10");
  let top10ScrollAmount = 0;
  let top10ScrollStep = 500;
  if (top10Slider && top10NextButton && top10PrevButton) {
    top10Slider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
    function getTop10MaxScroll() { return top10Slider.scrollWidth - top10Slider.clientWidth; }
    function updateTop10Buttons() {
      if (top10ScrollAmount <= 0) {
        top10PrevButton.closest(".prev-button-top10").style.display = "none";
      } else {
        top10PrevButton.closest(".prev-button-top10").style.display = "";
      }
      if (getTop10MaxScroll() <= 0 || top10ScrollAmount >= getTop10MaxScroll() - 5) {
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
  }

  // --- Comedy Slider Logic ---
  const comedySlider = document.querySelector(".action-images-container");
  const comedyNextButton = document.querySelector(".next-chevron-btn-comedy");
  const comedyPrevButton = document.querySelector(".prev-chevron-btn-comedy");
  let comedyScrollAmount = 0;
  let comedyScrollStep = 500;
  if (comedySlider && comedyNextButton && comedyPrevButton) {
    comedySlider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";
    function getComedyMaxScroll() { return comedySlider.scrollWidth - comedySlider.clientWidth; }
    function updateComedyButtons() {
      if (comedyScrollAmount <= 0) {
        comedyPrevButton.closest(".prev-button-comedy").style.display = "none";
      } else {
        comedyPrevButton.closest(".prev-button-comedy").style.display = "";
      }
      if (getComedyMaxScroll() <= 0 || comedyScrollAmount >= getComedyMaxScroll() - 5) {
        comedyNextButton.closest(".next-button-comedy").style.display = "none";
      } else {
        comedyNextButton.closest(".next-button-comedy").style.display = "";
      }
    }
    comedyNextButton.addEventListener("click", function () {
      comedyScrollAmount += comedyScrollStep;
      if (comedyScrollAmount > getComedyMaxScroll())
        comedyScrollAmount = getComedyMaxScroll();
      comedySlider.style.transform = `translateX(-${comedyScrollAmount}px)`;
      updateComedyButtons();
    });
    comedyPrevButton.addEventListener("click", function () {
      comedyScrollAmount -= comedyScrollStep;
      if (comedyScrollAmount < 0) comedyScrollAmount = 0;
      comedySlider.style.transform = `translateX(-${comedyScrollAmount}px)`;
      updateComedyButtons();
    });
    window.addEventListener("resize", function () {
      if (comedyScrollAmount > getComedyMaxScroll()) {
        comedyScrollAmount = getComedyMaxScroll();
        comedySlider.style.transform = `translateX(-${comedyScrollAmount}px)`;
      }
      updateComedyButtons();
    });
    updateComedyButtons();
  }

  // Set video start time for all videos with class 'video-episode'
  const videos = document.getElementsByClassName("video-episode");
  Array.from(videos).forEach(function (video) {
    video.addEventListener("loadedmetadata", function () {
      video.currentTime = 5;
    });
  });
});