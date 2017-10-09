import {apiEndpoint} from './Api';

export const FieldOfStudyApi = {

  get: async (departmentId) => {
    const res = await fetch(`${apiEndpoint}/departments/${departmentId}/fieldofstudies`);
    return res.json();
  }
};
