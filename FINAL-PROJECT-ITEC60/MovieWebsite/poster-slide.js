
document.addEventListener('DOMContentLoaded', function () {
  
 // --- Slider Logic ---
  const slider = document.querySelector('.top-10-images-container');
  const nextButton = document.querySelector('.next-chevron-btn-top10');
  const prevButton = document.querySelector('.prev-chevron-btn-top10');

  // Add smooth transition for sliding
  slider.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';

  // Hide the prev button at start
  prevButton.closest('.prev-button-top10').style.display = 'none';

  let scrollAmount = 0;
  let scrollStep = 500; // You can adjust this for your card width

  function getMaxScroll() {
    return slider.scrollWidth - slider.clientWidth;
  }

  function updateButtons() {
    // Hide prev if at start, show otherwise
    if (scrollAmount <= 0) {
      prevButton.closest('.prev-button-top10').style.display = 'none';
    } else {
      prevButton.closest('.prev-button-top10').style.display = '';
    }
    // Hide next if at end, show otherwise
    if (getMaxScroll() <= 0 || scrollAmount >= getMaxScroll() - 5) {
      nextButton.closest('.next-button-top10').style.display = 'none';
    } else {
      nextButton.closest('.next-button-top10').style.display = '';
    }
  }

  nextButton.addEventListener('click', function () {
    scrollAmount += scrollStep;
    if (scrollAmount > getMaxScroll()) scrollAmount = getMaxScroll();
    slider.style.transform = `translateX(-${scrollAmount}px)`;
    updateButtons();
  });

  prevButton.addEventListener('click', function () {
    scrollAmount -= scrollStep;
    if (scrollAmount < 0) scrollAmount = 0;
    slider.style.transform = `translateX(-${scrollAmount}px)`;
    updateButtons();
  });

  window.addEventListener('resize', function () {
    if (scrollAmount > getMaxScroll()) {
      scrollAmount = getMaxScroll();
      slider.style.transform = `translateX(-${scrollAmount}px)`;
    }
    updateButtons();
  });

  // Initial state
  updateButtons();

});