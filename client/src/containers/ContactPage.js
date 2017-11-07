import React, {Component} from 'react';
import './HomePage.css';
import { Grid } from 'semantic-ui-react';
import ContactDepartment from '../components/ContactDepartment';
import './ContactPage.css';

class ContactPage extends Component {
  render() {
      const departments = [
          {school: "NTNU", email: "ntnu@mail", adress: "abcveien 123", text: "Norges teknisk-naturvitenskapelig universitet "},
          {school: "UiO", email: "uio@mail", adress: "abcveien 234", text: "Universitetet i Oslo"},
          {school: "NMBU", email: "nmbu@mail", adress: "abcveien 567", text: "Norges miljø- og biovitenskapelige universitet"},
          {school: "Hist", email: "hist@mail", adress: "abcveien 890", text: "Norges teknisk-naturvitenskapelige universitet"},
          {school: "UiT", email: "uit@mail", adress: "abcveien 123", text: "Universitetet i Tromsø - Norges arktiske universitet"}
      ];
    return (
        <div className="contact-page">
            <Grid>
                <Grid.Column width={16}>
                    <div className="contact-main">
                        <ContactDepartment departments={departments} />
                    </div>
                </Grid.Column>
            </Grid>
        </div>
    );
  }
}

export default ContactPage;
