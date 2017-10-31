import React, {Component} from 'react';
import {Grid} from 'semantic-ui-react';
import './ContactDepartment.css';

class ContactDepartment extends Component {
    render() {
        return (
            <div className="contact-page">
                <Grid centered>
                    <Grid.Row>
                        <Grid.Column width={9}>
                            <h1>Kontakt oss</h1>
                            <p>Før du tar kontakt med oss, må du huske å sjekke ut <a href={"om-oss#FAQ"}>ofte stilte spørsmål!</a></p>
                        </Grid.Column>
                    </Grid.Row>
                    <Grid.Row columns={2}>
                        <Grid.Column width={5}>
                            <ContactUsForm/>
                        </Grid.Column>
                        <Grid.Column width={8}>
                            <Grid.Row>
                                <p>Contact info</p>
                            </Grid.Row>
                            <Grid.Row>
                                <p>Map</p>
                            </Grid.Row>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </div>
        );
    }
}

export default ContactDepartment;