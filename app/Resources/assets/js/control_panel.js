$(".nav-link.active").each(function() {
  const link = $(this);
  link.closest(".nav-dropdown").addClass("open")
});
