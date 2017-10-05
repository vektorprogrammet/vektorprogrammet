import {apiEndpoint} from './Api';

export const DepartmentAPI = {
  getAll: async () => {
    const res = await fetch(`${apiEndpoint}/departments`);
    return res.json();
  },

  get: async (id) => {
    const res = await fetch(`${apiEndpoint}/departments/${id}`);
    return res.json();
  }
};
