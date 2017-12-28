import createSagaMiddleware from 'redux-saga';
import { createStore, combineReducers, applyMiddleware } from 'redux';
import * as reducers from './reducers';
import { newsletterReducer } from './reducers/newsletter';
import rootSaga from './sagas/sagas';
import { reducer as formReducer } from 'redux-form';
import { saveState, loadState } from './localStorage';
import { composeWithDevTools } from 'redux-devtools-extension';

const combinedReducers = combineReducers({
  application: reducers.applicationReducer,
  departments: reducers.departmentReducer,
  preferredDepartment: reducers.preferredDepartmentReducer,
  sponsors: reducers.sponsorReducer,
  user: reducers.userReducer,
  form: formReducer,
    newsletter: newsletterReducer
});
const persistedState = loadState();
const sagaMiddleware = createSagaMiddleware();
const store = createStore(
  combinedReducers,
  persistedState,
  composeWithDevTools(
    applyMiddleware(sagaMiddleware),
  )
);
sagaMiddleware.run(rootSaga);

store.subscribe(() => {
  const state = store.getState();
  saveState({
    user: state.user,
    departments: state.departments,
    sponsors: state.sponsors,
    preferredDepartment: state.preferredDepartment
  })
});

export default store;
