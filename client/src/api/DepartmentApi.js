import {apiEndpoint} from './Api';

export const DepartmentApi = {
    getAll: async() => {
        const res = await fetch(`${apiEndpoint}/departments`);
        return res.json();
    },

    get: async(id) => {
        const res = await fetch(`${apiEndpoint}/departments/${id}`);
        return res.json();
    },

    getByActiveSemester: async() => {
        const res = await fetch(`${apiEndpoint}/departments/admissions/active`);
        return res.json();
    },

    getTeams: async(id) => {
        const res = await fetch(`${apiEndpoint}/departments/${id}/teams`);
        return res.json();
    }
};
