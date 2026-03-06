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

function removeEmptyNavCategories() {
  const navTitles = $('.nav-title');
  navTitles.each(function() {
    const title = $(this);
    if (title.prev().hasClass('nav-title')) {
      title.prev().remove();
    }
  });
}

removeEmptyNavCategories();
