function get(path: string) {
  return fetch(path);
}

function post(path: string, data: any) {
  return fetch(path, {
    method: 'POST',
    body: createSearchParams(data),
    credentials: 'include',
    headers: new Headers({
      'content-type': 'application/x-www-form-urlencoded',
    }),
  });
}

function put(path: string, data: any) {
  return fetch(path, {
    method: 'PUT',
    credentials: 'include',
    body: JSON.stringify(data),
  });
}

function del(path: string) {
  return fetch(path, {
    credentials: 'include',
    method: 'DELETE',
  });
}

function createSearchParams(data: any) {
  return Object.keys(data)
    .map(key => {
      return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
    })
    .join('&');
}

export default {
  get,
  post,
  put,
  delete: del,
};
