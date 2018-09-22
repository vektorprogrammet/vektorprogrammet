import http from './http.service'

function login(username, password) {
  return http.post("/account/login", {username, password})
}

function logout() {
  return http.get("/logout")
}

function getUser() {
  return http.get("/user")
}

export const accountService = {
  login,
  logout,
  getUser
};
