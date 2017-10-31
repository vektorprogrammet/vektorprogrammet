import './ContactInformation.css';
import React, {Component} from 'react';
import {Grid} from 'semantic-ui-react';

// TODO: Fix styling, didn't work after implementing map function
class ContactInformation extends Component {
    render() {
        return (
            <Grid>
                <Grid.Row>
                    <h1>Kontakt info</h1>
                </Grid.Row>
                <Grid.Row>
                    <h2>E-mail</h2>
                    <p>af</p>
                </Grid.Row>
            </Grid>
        );
    }
}

export default ContactInformation;
