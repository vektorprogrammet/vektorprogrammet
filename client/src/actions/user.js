export const REQUEST_USER_UPDATE = 'REQUEST_USER_UPDATE';
export const USER_UPDATED = 'USER_UPDATED';

export const requestUserUpdate = (user) => ({
  type: REQUEST_USER_UPDATE,
  payload: user
});

export const userUpdated = (user) => ({
  type: USER_UPDATED,
  payload: user
});


