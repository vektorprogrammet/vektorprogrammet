import { all } from 'redux-saga/effects';

import { watchLoginSaga, watchLogoutSaga } from './sagas/authenticationSaga';
import * as sponsorSagas from './sagas/sponsorSaga';
import * as departmentSagas from './sagas/departmentSaga';
import * as applicationSagas from './sagas/applicationSaga';

export default function* rootSaga() {
  yield all([
    watchLoginSaga(),
    watchLogoutSaga(),
    departmentSagas.fetchDepartmentsSaga(),
    sponsorSagas.fetchSponsorsSaga(),
    applicationSagas.postApplicationSaga(),
  ]);
}

