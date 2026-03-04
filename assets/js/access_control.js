const links = document.querySelectorAll('a');

for (let i = 0; i < links.length; i ++) {
  const link = links[i];
  if (link.href.indexOf("#noaccess") !== -1) {
    link.parentNode.removeChild(link);
  }
}

const forms = document.querySelectorAll('form');

for (let i = 0; i < forms.length; i ++) {
  const form = forms[i];
  if (form.action.indexOf("#noaccess") !== -1) {
    form.parentNode.removeChild(form);
  }
}
