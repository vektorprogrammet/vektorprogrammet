const links = document.querySelectorAll('a');

for (let i = 0; i < links.length; i ++) {
  const link = links[i];
  if (link.href.indexOf("#noaccess") !== -1) {
    link.parentNode.removeChild(link);
  }
}
