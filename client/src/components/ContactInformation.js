import './ContactInformation.css';
import React, {Component} from 'react';

// TODO: Fix styling, didn't work after implementing map function
class ContactInfo extends Component {
    render() {
        const contactInfo = this.props.contactInfo.map((item, index) =>
            <li key={index} className={'list-'+item.school}>
                <a href={'mailto:'+item.email}>{item.place}</a>
            </li>
        );
        return (
            <section>
                <ul className="unordered-list list-container">
                      Kontakt: {contactInfo}
                </ul>
            </section>
        );
    }
}

export default ContactInfo;
