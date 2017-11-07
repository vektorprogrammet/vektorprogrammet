import {loginEndpoint} from '../api/Api';

export const Authentication = {
    login: (username, password) => {
        return fetch(loginEndpoint, {
                headers: {
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({_username: username, _password: password})
            }
        )
            // .catch(err => console.log("Hurhur"));
    }
};
