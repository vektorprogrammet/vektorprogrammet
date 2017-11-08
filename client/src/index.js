import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import { Provider } from 'react-redux';
import './index.css';
import App from './containers/App';
import registerServiceWorker from './registerServiceWorker';

import store from './store';

const rootEl = document.getElementById('root');

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
