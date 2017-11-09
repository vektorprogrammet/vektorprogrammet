import {apiEndpoint} from './Api';

export const SponsorApi = {
  getAll: () => {
    return fetch(`${apiEndpoint}/sponsors`);
  },

  get: async (id) => {
    const res = await fetch(`${apiEndpoint}/sponsors/${id}`);
    return res.json();
  }
};
