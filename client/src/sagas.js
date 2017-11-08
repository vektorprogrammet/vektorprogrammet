import { all } from 'redux-saga/effects';

import { watchLoginSaga, watchLogoutSaga } from './sagas/authenticationSaga';
import * as departmentSagas from './sagas/departmentSaga';
import * as applicationSagas from './sagas/applicationSaga';

export default function* rootSaga() {
  yield all([
    watchLoginSaga(),
    watchLogoutSaga(),
    departmentSagas.fetchDepartmentsSaga(),
    applicationSagas.postApplicationSaga(),
  ]);
}

