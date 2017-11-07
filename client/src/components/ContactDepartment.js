import React, {Component} from 'react';
import {List, Button} from 'semantic-ui-react';
import './ContactDepartment.css';
import ContactUsPopUp from '../components/ContactUsPopUp';
/*import ContactUsForm from './ContactUsForm';
import {Grid} from 'semantic-ui-react';
import ContactInformation from './ContactInformation';
import MapContainer from './MapContainer';*/

class ContactDepartment extends Component {
    constructor() {
        super();
        this.state = {
            showModal: false,
            width: window.innerWidth
        };
    }

    handleModal = () => {
        this.setState(prevState => ({
            showModal: !prevState.showModal
        }));
    };

    handleWindowSizeChange = () => {
        console.log("Resizing :) :)");
        this.setState({ width: window.innerWidth });
    };

    componentWillMount() {
        window.addEventListener('resize', this.handleWindowSizeChange);
    }

    componentWillUnmount() {
        window.removeEventListener('resize', this.handleWindowSizeChange);
    }
    render() {
        const departments = this.props.departments.map((item, index) =>
            <List.Item className={"contact-listSchool"} key={index}>
                <h2>{item.school}</h2>
                <p className={"contact-text"}>{item.text}</p>
                <p >{item.email}</p>
                <p>{item.adress}</p>
                <Button primary onClick={this.handleModal}>Kontakt oss!</Button>
                <br/><br/>
            </List.Item>
        );
        return (
            <div className="contact-department">
                <ContactUsPopUp windowWidth={this.state.width} show={this.state.showModal} onClose={this.handleModal}/>
                <List>
                    {departments}
                </List>
            </div>
        );
    }
}
export default ContactDepartment;