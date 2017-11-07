import React, {Component} from 'react';
import './HomePage.css';
import ContactDepartment from '../components/ContactDepartment';

class ContactPage extends Component {
  render() {
    return (
        <div className="contact-page">
            <ContactDepartment school={'NTNU'} address={"abc veien 123"} email={"Olanordmann@gmail.com"} />
        </div>
    );
  }
}

export default ContactPage;
