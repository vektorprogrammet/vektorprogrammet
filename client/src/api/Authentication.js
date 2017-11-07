import {loginEndpoint, logoutEndpoint} from '../api/Api';

export const Authentication = {
    login: (payload) => {
        const {username, password} = payload;
        return fetch(loginEndpoint, {
                headers: {
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({_username: username, _password: password})
            }
        )
    },

    logout: () => {
        return fetch(logoutEndpoint);
    }
};
