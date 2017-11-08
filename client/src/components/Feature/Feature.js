import React from 'react';
import {Grid} from 'semantic-ui-react';
import './Feature.css';

export default ({img, header, content}) => (
  <Grid.Column mobile={16} tablet={5} computer={5} widescreen={5} className="one centered feature">
    <img src={img} alt={header}/>
    <h3>{header}</h3>
    <p className="centered">{content}</p>
  </Grid.Column>
)
