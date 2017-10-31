import React, {Component} from 'react';
import {Grid} from 'semantic-ui-react';
import './ContactDepartment.css';
import ContactUsForm from './ContactUsForm';
import ContactInformation from './ContactInformation';
import MapWithLocation from './MapWithLocation';

class ContactDepartment extends Component {
    render() {
        return (
            <div className="contact-department">
                <Grid centered>
                    <Grid.Row>
                        <Grid.Column width={9}>
                            <h1> {this.props.school} - Kontakt oss</h1>
                            <p>Før du tar kontakt med oss, må du huske å sjekke ut <a href={"om-oss#FAQ"}>ofte stilte spørsmål!</a></p>
                        </Grid.Column>
                    </Grid.Row>
                    <Grid.Row columns={2}>
                        <Grid.Column width={5}>
                            <ContactUsForm/>
                        </Grid.Column>
                        <Grid.Column width={8}>
                            <Grid.Row>
                                <ContactInformation address={this.props.address} email={this.props.email} />
                            </Grid.Row>
                            <Grid.Row>
                                <MapWithLocation/>
                            </Grid.Row>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </div>
        );
    }
}

export default ContactDepartment;