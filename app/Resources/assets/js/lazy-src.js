document.addEventListener('DOMContentLoaded', lazyLoadImages);

function lazyLoadImages() {
  var lazyImages = document.querySelectorAll('img[lazy-src]');

  for (var i = 0; i < lazyImages.length; i++) {
    var img = lazyImages[i];
    var src = img.getAttribute('lazy-src');

    if (src) {
      img.src = src;
    }
  }
}
