import React, {Component} from 'react';
import './HomePage.css';
import ContactDepartment from '../components/ContactDepartment';

class ContactPage extends Component {
  render() {
    return (
        <ContactDepartment school={'NTNU'} address={"abc veien 123"} email={"Olanordmann@gmail.com"} />
    );
  }
}

export default ContactPage;
