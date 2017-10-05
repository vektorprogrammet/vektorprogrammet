import {apiEndpoint} from './Api';

export const FieldOfStudyAPI = {

  get: async (departmentId) => {
    const res = await fetch(`${apiEndpoint}/departments/${departmentId}/fieldofstudies`);
    return res.json();
  }
};
