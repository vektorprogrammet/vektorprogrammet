import { call, put, takeLatest } from 'redux-saga/effects';

import { FETCH_DEPARTMENTS, FETCH_CLOSEST_DEPARTMENT, setDepartments, setPreferredDepartment } from '../actions/department';
import { DepartmentApi } from '../api/DepartmentApi';

function* fetchDepartments() {
  try {
    const response = yield call(DepartmentApi.getAll);

    if (response.status !== 200) {
      console.error("Failed to fetch departments:", response);
      return;
    }

    const departments = yield response.json();
    yield put(setDepartments(departments));

  } catch (e) {
    console.log("Failed to fetch departments:", e);
  }
}

export function* fetchDepartmentsSaga() {
  yield takeLatest(FETCH_DEPARTMENTS, fetchDepartments);
}

function* fetchClosestDepartment() {
  try {
    const response = yield call(DepartmentApi.getClosest);

    if (response.status !== 200) {
      console.error("Failed to fetch closest department:", response);
      return;
    }

    const department = yield response.json();
    yield put(setPreferredDepartment(department.id));

  } catch (e) {
    console.log("Failed to fetch closest department:", e);
  }
}

export function* fetchClosestDepartmentSaga() {
  yield takeLatest(FETCH_CLOSEST_DEPARTMENT, fetchClosestDepartment);
}
