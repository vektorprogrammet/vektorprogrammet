import React, {Component} from 'react';
import './HomePage.css';
import ContactDepartment from '../components/ContactDepartment';
import DepartmentCardContainer from "../components/DepartmentCardContainer";

class ContactPage extends Component {
  render() {
    return (
        <div className="contact-page">
            <DepartmentCardContainer/>
            <ContactDepartment school={'NTNU'} address={"abc veien 123"} email={"Olanordmann@gmail.com"} />
        </div>
    );
  }
}

export default ContactPage;
