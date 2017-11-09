import createSagaMiddleware from 'redux-saga';
import { createStore, combineReducers, applyMiddleware } from 'redux';
import { userReducer } from './reducers/user';
import { sponsorReducer } from './reducers/sponsor';
import { departmentReducer } from './reducers/department';
import { applicationReducer } from './reducers/application';
import rootSaga from './sagas';
import { reducer as formReducer } from 'redux-form';
import { saveState, loadState } from './localStorage';
import { composeWithDevTools } from 'redux-devtools-extension';

const reducers = combineReducers({
  application: applicationReducer,
  departments: departmentReducer,
  sponsors: sponsorReducer,
  user: userReducer,
  form: formReducer,
});
const persistedState = loadState();
const sagaMiddleware = createSagaMiddleware();
const store = createStore(
  reducers,
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
  })
});

export default store;
