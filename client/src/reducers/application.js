import * as actions from '../actions/application';

export const applicationReducer = (state = {}, action) => {
  switch (action.type) {
    case actions.APPLICATION_CREATED:
      return action.payload;
    default:
      return state
  }
};
