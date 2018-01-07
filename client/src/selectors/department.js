import { createSelector } from 'reselect';

const getPreferredDepartment = state => state.preferredDepartment;
const getDepartments = state => state.departments;
const getUser = state => state.user;

const getPreferredDepartmentId = createSelector(
  [getUser, getPreferredDepartment],
  (user, preferredDepartment) => {
    console.log(user);
    if (user.hasOwnProperty('department')) {
      return user.department;
    }
    return preferredDepartment;
  }
);

export const getSortedDepartments = createSelector(
  [getDepartments, getPreferredDepartmentId],
  (departments, preferredDepartmentId) => (
    departments.sort((a, b) => {
      if (a.id === preferredDepartmentId) {
        return -1
      }
      if (b.id === preferredDepartmentId) {
        return 1
      }
      if (a.name > b.name) {
        return -1;
      }
      if (a.name < b.name) {
        return 1;
      }
      return 0;
    })
  )
);

export const getActiveDepartments = createSelector(
  [getSortedDepartments],
  (departments) => departments.filter(d => d.active_admission)
);

export const getInactiveDepartments = createSelector(
  [getSortedDepartments],
  (departments) => departments.filter(d => !d.active_admission)
);
