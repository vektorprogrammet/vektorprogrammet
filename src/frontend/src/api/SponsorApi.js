export const SponsorApi = {
  getAll: async () => {
    const res = await fetch('http://localhost:8000/api/sponsors');
    return await res.json();
  },
  get: async (id) => {
    const res = await fetch(`http://localhost:8000/api/sponsors/${id}`);
    return await res.json();
  }
};
