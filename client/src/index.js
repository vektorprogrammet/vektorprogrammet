import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import { Provider } from 'react-redux';
import createSagaMiddleware from 'redux-saga';
import { createStore, combineReducers, applyMiddleware } from 'redux';
import './index.css';
import App from './containers/App';
import registerServiceWorker from './registerServiceWorker';
import { userReducer } from './reducers/user';
import { departmentReducer } from './reducers/department';
import rootSaga from './sagas';

const rootEl = document.getElementById('root');

const sagaMiddleware = createSagaMiddleware();
const reducers = combineReducers({
  departments: departmentReducer,
  user: userReducer
});
const store = createStore(
  reducers,
  window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__(),
  applyMiddleware(sagaMiddleware),
);
sagaMiddleware.run(rootSaga);

ReactDOM.render((
  <Provider store={store}>
    <BrowserRouter>
      <App/>
    </BrowserRouter>
  </Provider>
), rootEl);

if (module.hot) {
  module.hot.accept('./containers/App', () => {
    const NextApp = require('./containers/App').default;
    ReactDOM.render((
        <Provider store={store}>
          <BrowserRouter>
            <NextApp/>
          </BrowserRouter>
        </Provider>
      ), rootEl,
    );
  });
}
registerServiceWorker();
