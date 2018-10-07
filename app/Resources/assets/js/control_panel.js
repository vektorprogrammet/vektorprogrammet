$('.nav-link.active').each(function () {
  const link = $(this);
  link.closest('.nav-dropdown').addClass('open');
});

$('.nav-dropdown-toggle').each(function () {
  const toggle = $(this);
  toggle.click(function (e) {
    if (e.target !== e.currentTarget) {
      toggle.closest('.nav-dropdown').toggleClass('open');
    }
  });
});
