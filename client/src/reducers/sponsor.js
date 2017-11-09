import * as actions from '../actions/sponsor';

export const sponsorReducer = (state = [], action) => {
    switch (action.type) {
        case actions.SET_SPONSORS:
            return action.payload;
        default:
            return state
    }
};
