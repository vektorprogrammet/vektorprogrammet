const initialState = {
  loggedIn: false
};

export const userReducer = (state = initialState, action) => {
  switch (action.type) {
    case 'SET_USER':
      return {...state, ...action.payload, logged_in: true};
    case 'REMOVE_USER':
      return {...initialState};
    default:
      return state;
  }
};
