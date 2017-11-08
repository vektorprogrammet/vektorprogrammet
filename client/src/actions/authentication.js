export const REQUEST_LOGIN = 'REQUEST_LOGIN';
export const REQUEST_LOGOUT = 'REQUEST_LOGOUT';
export const SET_USER = 'SET_USER';
export const REMOVE_USER = 'REMOVE_USER';

export const requestLogin = (payload) => ({
  type: REQUEST_LOGIN,
  payload,
});

export const requestLogout = () => ({
  type: REQUEST_LOGOUT,
  payload: {},
});

export const setUser = (user) => ({
  type: SET_USER,
  payload: user
});

export const removeUser = () => ({
  type: SET_USER,
  payload: {}
});
