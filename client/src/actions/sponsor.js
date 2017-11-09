export const FETCH_SPONSORS = 'FETCH_SPONSORS';
export const SET_SPONSORS = 'SET_SPONSORS';

export const fetchSponsors = () => ({
  type: FETCH_SPONSORS,
  payload: {}
});

export const setSponsors = (sponsors) => ({
  type: SET_SPONSORS,
  payload: sponsors
});

