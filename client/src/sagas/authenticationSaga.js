import { call, put, takeLatest } from 'redux-saga/effects';

import { REQUEST_LOGIN, REQUEST_LOGOUT, setUser, removeUser } from '../actions/authentication';
import { Authentication } from '../api/Authentication';

function* loginSaga(action) {
  try {
    const response = yield call(Authentication.login, action.payload);
    if (response.status !== 200) {
      return console.error(response);
    }

    const body = yield response.json();
    const user = JSON.parse(body.user);
    yield put(setUser(user));
  } catch (e) {
    console.log(e);
  }
}

function* logoutSaga(action) {
  try {
    const response = yield call(Authentication.logout, action.payload);
    if (response.status !== 200) {
      return console.error(response);
    }

    yield put(removeUser());
  } catch (e) {
    console.log(e);
  }
}

export function* watchLoginSaga() {
  yield takeLatest(REQUEST_LOGIN, loginSaga);
}

export function* watchLogoutSaga() {
  yield takeLatest(REQUEST_LOGOUT, logoutSaga);
}
