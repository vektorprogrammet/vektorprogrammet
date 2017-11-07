import React, {Component} from 'react';
import {List} from 'semantic-ui-react';
import './ContactDepartment.css';
/*import ContactUsForm from './ContactUsForm';
import {Grid} from 'semantic-ui-react';
import ContactInformation from './ContactInformation';
import MapContainer from './MapContainer';*/

class ContactDepartment extends Component {
    render() {
        const schools = this.props.departments.map((item, index) =>
            <List.Item className="contact-listSchool" key={index}>
                <div>
                    {item.school}
                </div>
            </List.Item>
        );
        return (
            <div className="contact-department">
                <List>
                        {schools}
                </List>
            </div>
        );
    }
}

export default ContactDepartment;