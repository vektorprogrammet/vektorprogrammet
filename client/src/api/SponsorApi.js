import {apiEndpoint} from './Api';

export const SponsorApi = {
  getAll: async () => {
    const res = await fetch(`${apiEndpoint}/sponsors`);
    return res.json();
  },

  get: async (id) => {
    const res = await fetch(`${apiEndpoint}/sponsors/${id}`);
    return res.json();
  }
};
