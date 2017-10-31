import './ContactInformation.css';
import React, {Component} from 'react';
import {Grid} from 'semantic-ui-react';

// This component is not in use
class ContactInformation extends Component {
    render() {
        return(
            <div>
                <Grid>
                    {/*TODO: Make this look nicer*/}
                    <Grid.Row>
                            <h3>E-mail: </h3>
                            <p>{this.props.email}</p>
                    </Grid.Row>
                    <Grid.Row>
                        <h3>Adresse: </h3>
                        <p>{this.props.address}</p>
                    </Grid.Row>
                </Grid>
            </div>
        );
    }
}

export default ContactInformation;
