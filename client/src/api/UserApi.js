import {apiEndpoint} from './Api';

export const UserApi = {
    getAll: async () => {
        const res = await fetch(`${apiEndpoint}/users`);
        return res.json();
    },

    get: async (id) => {
        const res = await fetch(`${apiEndpoint}/users/${id}`);
        return res.json();
    },
}
