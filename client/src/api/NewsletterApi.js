import {apiEndpoint} from './Api';

export const NewsletterApi = {
    getAll: async () => {
        const res = await fetch(`${apiEndpoint}/newsletters`);
        return res.json();
    },

    get: async (id) => {
        const res = await fetch(`${apiEndpoint}/newsletters/${id}`);
        return res.json();
    },

    getByDepartmentShortName: async (shortname) => {
        const res = await fetch(`${apiEndpoint}/newsletters/${shortname}/department`);
        return res.json();
    }
};
