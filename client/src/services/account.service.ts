import http from './http.service';

async function login(username: string, password: string) {
  const response = await http.post('/api/account/login', {
    username,
    password,
  });
  return await response.json();
}

function logout() {
  return http.get('/logout');
}

async function getUser() {
  const response = await http.get('/api/account/user');
  return await response.json();
}

async function getScheduleInfo() {
  const response = await http.get('/api/account/mypartner');
  return await response.json();
}

async function getLocationByName(input: string='Gimse Ungdomsskole') {
    const formatted_input = input.replace(/\s+/g, '+');
    console.log(formatted_input);
    const api_key = 'AIzaSyCfhjte_te7uOqtXmZkvtrhdZaNMaVIGso';
    const response = await http.get('https://maps.googleapis.com/maps/api/geocode/json?address=' +
    formatted_input + '&key=' + api_key);
    return await response.json()
}

export const accountService = {
  login,
  logout,
  getUser,
  getScheduleInfo,
  getLocationByName,
};
