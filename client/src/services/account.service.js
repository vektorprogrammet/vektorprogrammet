import http from './http.service'

async function login(username, password) {
  const response = await http.post("/api/account/login", {username, password})
  return await response.json();
}

async function logout() {
  const response = await http.get("/api/account/logout");
  return await response.json();
}

async function getUser() {
  const response = await http.get("/api/account/user");
  return await response.json();
}

export const accountService = {
  login,
  logout,
  getUser
};
