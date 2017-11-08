import createSagaMiddleware from 'redux-saga';
import { createStore, combineReducers, applyMiddleware } from 'redux';
import { userReducer } from './reducers/user';
import { departmentReducer } from './reducers/department';
import { applicationReducer } from './reducers/application';
import rootSaga from './sagas';
import { reducer as formReducer } from 'redux-form';
import { saveState, loadState } from './localStorage';

const reducers = combineReducers({
  application: applicationReducer,
  departments: departmentReducer,
  user: userReducer,
  form: formReducer,
});
const persistedState = loadState();
const sagaMiddleware = createSagaMiddleware();
const store = createStore(
  reducers,
  persistedState,
  applyMiddleware(sagaMiddleware),
);
sagaMiddleware.run(rootSaga);

store.subscribe(() => {
  const state = store.getState();
  saveState({
    user: state.user,
    departments: state.departments
  })
});

export default store;
