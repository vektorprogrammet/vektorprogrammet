(function() {
  var links = document.querySelectorAll('a[smooth-scroll]');
  var targetOffset, currentPosition,
      body = document.body,
      animateTime = 900;

  var height = Math.max( body.scrollHeight, body.offsetHeight );

  var browserHeight = window.innerHeight;

  var maxScrollHeight = height - browserHeight;

  function getPageScroll() {
    var yScroll;

    if (window.pageYOffset) {
      yScroll = window.pageYOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {
      yScroll = document.documentElement.scrollTop;
    } else if (document.body) {
      yScroll = document.body.scrollTop;
    }
    return yScroll;
  }

  for (var i = 0; i < links.length; i++) {
    var link = links[i];
    link.addEventListener('click', function (event) {

      targetOffset = document.getElementById(event.target.hash.substr(1)).offsetTop;
      targetOffset = Math.min(targetOffset, maxScrollHeight);
      targetOffset -= 1;
      currentPosition = getPageScroll();

      body.classList.add('in-transition');
      var translate = "translate(0, -" + (targetOffset - currentPosition) + "px)";
      body.style.WebkitTransform = translate;
      body.style.MozTransform = translate;
      body.style.transform = translate;

      window.setTimeout(function () {
        body.classList.remove('in-transition');
        body.style.cssText = "";
        window.scrollTo(0, targetOffset);
      }, animateTime);

      event.preventDefault();

    }, false);
  }
})();
