import { all } from 'redux-saga/effects';

import { watchLoginSaga, watchLogoutSaga } from './authenticationSaga';
import * as sponsorSagas from './sponsorSaga';
import * as departmentSagas from './departmentSaga';
import * as applicationSagas from './applicationSaga';

export default function* rootSaga() {
  yield all([
    watchLoginSaga(),
    watchLogoutSaga(),
    departmentSagas.fetchDepartmentsSaga(),
    sponsorSagas.fetchSponsorsSaga(),
    applicationSagas.postApplicationSaga(),
  ]);
}

