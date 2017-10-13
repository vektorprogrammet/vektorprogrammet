import {apiEndpoint} from './Api';

export const TeamApi = {
    getAll: async () => {
        const res = await fetch(`${apiEndpoint}/teams`);
        return res.json();
    }
}
