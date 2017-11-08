export const FETCH_DEPARTMENTS = 'FETCH_DEPARTMENTS';
export const SET_DEPARTMENTS = 'SET_DEPARTMENTS';

export const FETCH_ACTIVE_DEPARTMENTS = 'FETCH_ACTIVE_DEPARTMENTS';
export const SET_ACTIVE_DEPARTMENTS = 'SET_ACTIVE_DEPARTMENTS';

export const fetchDepartments = () => ({
  type: FETCH_DEPARTMENTS,
  payload: {}
});

export const setDepartments = (departments) => ({
  type: SET_DEPARTMENTS,
  payload: departments
});

export const fetchActiveDepartments = () => ({
  type: FETCH_ACTIVE_DEPARTMENTS,
  payload: {}
});

export const setActiveDepartments = (departments) => ({
  type: SET_ACTIVE_DEPARTMENTS,
  payload: departments
});
