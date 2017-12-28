export const FETCH_DEPARTMENTS = 'FETCH_DEPARTMENTS';
export const SET_DEPARTMENTS = 'SET_DEPARTMENTS';
export const SET_PREFERRED_DEPARTMENT = 'SET_PREFERRED_DEPARTMENT';
export const FETCH_CLOSEST_DEPARTMENT = 'FETCH_CLOSEST_DEPARTMENT';

export const fetchDepartments = () => ({
  type: FETCH_DEPARTMENTS,
  payload: {}
});

export const setDepartments = (departments) => ({
  type: SET_DEPARTMENTS,
  payload: departments
});

export const fetchPreferredDepartment = () => ({
  type: FETCH_CLOSEST_DEPARTMENT,
  payload: {}
});

export const setPreferredDepartment = (department) => ({
  type: SET_PREFERRED_DEPARTMENT,
  payload: department
});

