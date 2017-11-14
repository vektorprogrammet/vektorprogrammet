import * as actions from '../actions/newsletter';

const initialState = {
    submitting: false,
    hasSubmitted: false
};
export const newsletterReducer = (state = initialState, action) => {
    switch (action.type) {
        case actions.NEWSLETTER_CREATED:
            return {submitting: false, hasSubmitted: true};
        case actions.POST_NEWSLETTER:
            return {submitting: true, hasSubmitted: false};
        default:
            return state
    }
};
