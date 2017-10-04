import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter} from 'react-router-dom'
import './index.css';
import App from './containers/App';
import registerServiceWorker from './registerServiceWorker';

const rootEl = document.getElementById('root');

ReactDOM.render((
    <BrowserRouter>
      <App />
    </BrowserRouter>
), rootEl);

if (module.hot) {
  module.hot.accept('./containers/App', () => {
    const NextApp = require('./containers/App').default;
    ReactDOM.render((
            <BrowserRouter>
              <NextApp />
            </BrowserRouter>
        ), rootEl
    )
  })
}
registerServiceWorker();
