import React, {Component} from 'react';
import {List} from 'semantic-ui-react';
import './ContactDepartment.css';
/*import ContactUsForm from './ContactUsForm';
import {Grid} from 'semantic-ui-react';
import ContactInformation from './ContactInformation';
import MapContainer from './MapContainer';*/

class ContactDepartment extends Component {
    render() {
        const departments = this.props.departments.map((item, index) =>
            <List.Item key={index}>
                <h2 className={"contact-listSchool"}>
                    {item.school}
                </h2>
                <p >
                    {item.email}
                </p>
                <p>
                    {item.adress}
                </p> <br/><br/>
            </List.Item>
        );
        return (
            <div className="contact-department">
                <List>
                        {departments}
                </List>
            </div>
        );
    }
}

export default ContactDepartment;