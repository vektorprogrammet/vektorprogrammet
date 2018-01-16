import {apiEndpoint} from './Api';

export const DepartmentApi = {
    getAll: () => {
        return fetch(`${apiEndpoint}/departments`);
    },

    get: async(id) => {
        const res = await fetch(`${apiEndpoint}/departments/${id}`);
        return res.json();
    },

    getByShortName: async(shortName) => {
        const res = await fetch(`${apiEndpoint}/departments/${shortName}/shortname`);
        return res.json();
    },

    getByActiveSemester: async() => {
        return fetch(`${apiEndpoint}/departments/admissions/active`);
    },

    getClosest: () => {
        return fetch(`${apiEndpoint}/closestdepartment`);
    }
};
