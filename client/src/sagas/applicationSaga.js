import { call, put, takeLatest } from 'redux-saga/effects';
import { ApplicationApi } from '../api/ApplicationApi';

import { POST_APPLICATION, applicationCreated } from '../actions/application';

function* postApplication(action) {
  try {
    const application = action.payload;
    application.departmentId = application.department.id;

    const response = yield call(ApplicationApi.post, action.payload);

    if (response.status !== 201) {
      console.error("Failed to create application:", response);
      return;
    }

    yield put(applicationCreated(application));
  } catch (e) {
    console.log("Failed to fetch departments:", e);
  }
}

export function* postApplicationSaga() {
  yield takeLatest(POST_APPLICATION, postApplication);
}
