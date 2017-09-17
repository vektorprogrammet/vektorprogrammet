import React from 'react';
import ReactDOM from 'react-dom';
import renderer from 'react-test-renderer';
import TeamPage from './TeamPage';

it('renders without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render(<TeamPage />, div);
});

it('renders correctly', () => {
  const tree = renderer.create(<TeamPage />).toJSON();
  expect(tree).toMatchSnapshot();
});
