import React from 'react';
import ReactDOM from 'react-dom';
import renderer from 'react-test-renderer';
import AssistantPage from './AssistantPage';

it('renders without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render(<AssistantPage />, div);
});

it('renders correctly', () => {
  const tree = renderer.create(<AssistantPage />).toJSON();
  expect(tree).toMatchSnapshot();
});
