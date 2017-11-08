import * as actions from '../actions/department';

export const departmentReducer = (state = [], action) => {
    switch (action.type) {
        case actions.SET_DEPARTMENTS:
            return action.payload;
        default:
            return state
    }
};
