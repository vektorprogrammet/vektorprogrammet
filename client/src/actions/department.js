export const FETCH_DEPARTMENTS = 'FETCH_DEPARTMENTS';
export const SET_DEPARTMENTS = 'SET_DEPARTMENTS';

export const fetchDepartments = () => ({
  type: FETCH_DEPARTMENTS,
  payload: {}
});

export const setDepartments = (departments) => ({
  type: SET_DEPARTMENTS,
  payload: departments
});

