import { call, put, takeLatest } from 'redux-saga/effects';

import { FETCH_SPONSORS, setSponsors } from '../actions/sponsor';
import { SponsorApi } from '../api/SponsorApi';

function* fetchSponsors() {
  try {
    const response = yield call(SponsorApi.getAll);

    if (response.status !== 200) {
      console.error("Failed to fetch sponsors:", response);
      return;
    }

    const sponsors = yield response.json();
    yield put(setSponsors(sponsors));

  } catch (err) {
    console.log("Failed to fetch sponsors:", err);
  }
}

export function* fetchSponsorsSaga() {
  yield takeLatest(FETCH_SPONSORS, fetchSponsors);
}
