import React, {Component} from 'react';
import './HomePage.css';
import ContactDepartment from '../components/ContactDepartment';

class ContactPage extends Component {
  render() {
      const departments = [
          {school: "NTNU", email: "ntnu@mail"},
          {school: "UiO", email: "ntnu@mail"},
          {school: "NMBU"},
          {school: "Hist"},
          {school: "Ås"}
      ];
    return (
        <div className="contact-page">
            <ContactDepartment departments={departments} />
        </div>
    );
  }
}

export default ContactPage;
