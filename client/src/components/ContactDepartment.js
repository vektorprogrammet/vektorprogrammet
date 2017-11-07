import React, {Component} from 'react';
import {List, Button, Grid, Image} from 'semantic-ui-react';
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
            <Grid.Row className={"contact-listSchool"} key={index}>
                <Grid.Column width={8}>
                    <List>
                        <List.Item><h2>{item.school}</h2></List.Item>
                        <List.Item><p className={"contact-text"}>{item.text}</p></List.Item>
                        <List.Item>
                            <List.Icon name='mail' />
                            <List.Content>
                                <a href={"mailto:" + item.email}><p >{item.email}</p></a>
                            </List.Content>
                        </List.Item>
                        <List.Item>
                            <List.Icon name='marker' />
                            <List.Content>
                                <p>{item.adress}</p>
                            </List.Content>
                        </List.Item>
                        <List.Item><Button primary onClick={this.handleModal}>Kontakt oss!</Button></List.Item>
                        <br/><br/>
                    </List>
                </Grid.Column>
                <Grid.Column width={8}>
                    <Image src={"http://via.placeholder.com/300x300/"}/>
                </Grid.Column>
            </Grid.Row>
        );
        return (
            <div className="contact-department">
                <ContactUsPopUp windowWidth={this.state.width} show={this.state.showModal} onClose={this.handleModal}/>
                <Grid celled>
                    {departments}
                </Grid>
            </div>
        );
    }
}
export default ContactDepartment;