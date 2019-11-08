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

async function getPartnerInfo() {
  const response = await http.get('/api/account/my-partner');
  return await response.json();
}

async function getScheduleInfo() {
  const response = await http.get('/api/account/my-schedule');
  return await response.json();
}

export const accountService = {
  login,
  logout,
  getUser,
  getScheduleInfo,
  getPartnerInfo,
};
