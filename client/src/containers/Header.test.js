import React from 'react';
import ReactDOM from 'react-dom';
import renderer from 'react-test-renderer';
import {BrowserRouter} from 'react-router-dom'
import Header from './Header';

it('renders without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render((
      <BrowserRouter>
        <Header />
      </BrowserRouter>
  ), div);
});

it('renders correctly', () => {
  const tree = renderer.create((
      <BrowserRouter>
        <Header />
      </BrowserRouter>
  )).toJSON();
  expect(tree).toMatchSnapshot();
});
