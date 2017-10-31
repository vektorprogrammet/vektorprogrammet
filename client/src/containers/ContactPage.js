import React, {Component} from 'react';
import './HomePage.css';
import {Grid} from 'semantic-ui-react';
import ContactInformation from '../components/ContactInformation';

class ContactPage extends Component {
    constructor(props){
        super(props);
        this.state={
            department: 'NTNU'
        };
    }

  render() {
    const contactSchoolInfo = [
        {place: "Trondheim - NTNU", email: "NTNU@gmail.com"},
        {school: "HiST", place: "Trondheim - HiST", email: "HIST@gmail.com"},
        {school: "NMBU", place: "Ås", email: "NMBU@gmail.com"},
        {school: "UiO", place: "Oslo", email: "UiO@gmail.com"}
    ];
    return (
        <div className="about-us-page">
          <p>This is the contact page!</p>
          <Grid>
            <Grid.Row>
              <Grid.Column width={8}>
                <p>Form goes here</p>
              </Grid.Column>
              <Grid.Column width={8}>
                < ContactInformation />
              </Grid.Column>
            </Grid.Row>
          </Grid>
        </div>
    );
  }
}

export default ContactPage;
