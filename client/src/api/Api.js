const baseUrl = "http://localhost:8000";
export const apiEndpoint = process.env.API_ENDPOINT || baseUrl + '/api';
export const loginEndpoint = baseUrl + '/login_check';
export const logoutEndpoint = baseUrl + '/logout';
