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

    post: async(postData) => {
        const result = await fetch(`${apiEndpoint}/newsletters`, {
            headers: {
                'Content-Type': 'application/json'
            },
            method: "POST",
            body: JSON.stringify(postData)
        });
        console.log(result.json());
    },

    getByDepartment: async (id) => {
        const res = await fetch(`${apiEndpoint}/newsletters/${id}/department`);
        return res.json();
    }
};
