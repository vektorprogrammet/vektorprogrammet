import {apiEndpoint} from './Api';

export const ApplicationApi = {
  getAll: async () => {
    const res = await fetch(`${apiEndpoint}/applications`);
    return res.json();
  },

  get: async (id) => {
    const res = await fetch(`${apiEndpoint}/applications/${id}`);
    return res.json();
  },

  post: async(application) => {
      await fetch(`${apiEndpoint}/applications`, {
          headers: {
              'Content-Type': 'application/json'
          },
          method: "POST",
          body: JSON.stringify(application)
      });
  },

  delete: async(id) => {
        await fetch(`${apiEndpoint}/applications/${id}`, {
            method: "DELETE"
        });
  }


};
