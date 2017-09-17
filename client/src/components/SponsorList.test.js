import React from 'react';
import ReactDOM from 'react-dom';
import renderer from 'react-test-renderer';
import SponsorList from './SponsorList';

const sponsors = [
  {
    id: 1,
    name: 'Sponsor 1',
    url: 'http://sponsor1.com',
    size: 'small',
    logo_image_path: 'http://placehold.it/100x100',
  },
  {
    id: 2,
    name: 'Sponsor 2',
    url: 'http://sponsor2.com',
    size: 'medium',
    logo_image_path: 'http://placehold.it/10x10',
  },
];

it('renders without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render(<SponsorList sponsors={sponsors} />, div);
});

it('renders "undefined" sponsors without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render(<SponsorList />, div);
});

it('renders correctly', () => {
  const tree = renderer.create(
      <SponsorList sponsors={sponsors} />
  ).toJSON();
  expect(tree).toMatchSnapshot();
});
