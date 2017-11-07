export const REQUEST_LOGIN = 'REQUEST_LOGIN';
export const REQUEST_LOGOUT = 'REQUEST_LOGOUT';

export const requestLogin = (payload) => ({
    type: REQUEST_LOGIN,
    payload
});

export const requestLogout = () => ({
    type: REQUEST_LOGOUT,
    payload: {}
});
