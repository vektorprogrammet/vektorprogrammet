import React, {Component} from 'react';
import './HomePage.css';
import {Grid} from 'semantic-ui-react';

class ContactPage extends Component {
  render() {
    return (
        <div className="about-us-page">
          <p>This is the contact page!</p>
          <Grid>
            <Grid.Row>
              <Grid.Column width={8}>
                <p>Form goes here</p>
              </Grid.Column>
              <Grid.Column width={8}>
                <p>Contact info goes here</p>
              </Grid.Column>
            </Grid.Row>
          </Grid>
        </div>
    );
  }
}

export default ContactPage;
