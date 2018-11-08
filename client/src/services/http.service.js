function get(path) {
  return fetch(path);
}

function post(path, data) {
  return fetch(path, {
    method: 'POST',
    body: createSearchParams(data),
    credentials: 'include',
    headers: new Headers({ 'content-type': 'application/x-www-form-urlencoded' }),
  });
}

function put(path, data) {
  return fetch(path, {
    method: 'PUT',
    credentials: 'include',
    body: JSON.stringify(data),
  });
}

function del(path) {
  return fetch(path, {
    credentials: 'include',
    method: 'DELETE',
  });
}

function createSearchParams(data) {
  return Object.keys(data).map(key => `${encodeURIComponent(key)}=${encodeURIComponent(data[key])}`).join('&');
}

export default {
  get,
  post,
  put,
  delete: del,
};
