import { call, put, takeLatest, all } from 'redux-saga/effects';

import { REQUEST_LOGIN, REQUEST_LOGOUT } from './actions/authentication';
import { Authentication } from './api/Authentication';

function* loginSaga(action) {
    try {
        const response = yield call(Authentication.login, action.payload);
        if (response.status !== 200) {
            throw response;
        }

        const body = yield response.json();
        const user = JSON.parse(body.user);
        yield put({
            type: 'SET_USER',
            payload: user,
        });
    } catch (e) {
        console.log(e);
    }
}

function* logoutSaga(action) {
    try {
        const response = yield call(Authentication.logout, action.payload);
        if (response.status !== 200) {
            throw response;
        }

        yield put({type: 'REMOVE_USER'});
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

export default function* rootSaga() {
    yield all([
        watchLoginSaga(),
        watchLogoutSaga(),
    ])
}

