import React, {Component} from 'react';
import './HomePage.css';
import './ContactPage.css';
import ChooseDepartment from "../components/ChooseDepartment";
import {Grid} from 'semantic-ui-react';

class ContactPage extends Component {
  render() {
    return (
        <div className="contact-page">
            <Grid stackable>
                <Grid.Row columns={4} style={{paddingLeft: 100, paddingRight: 100}}>
                    <Grid.Column style={{paddingLeft: 50, paddingRight: 25}}>
                        <ChooseDepartment departmentName={"NTNU"}/>
                    </Grid.Column>
                    <Grid.Column style={{paddingLeft: 50, paddingRight: 25}}>
                        <ChooseDepartment departmentName={"HiST"}/>
                    </Grid.Column>
                    <Grid.Column style={{paddingLeft: 50, paddingRight: 25}}>
                        <ChooseDepartment departmentName={"NMBU"}/>
                    </Grid.Column>
                    <Grid.Column style={{paddingLeft: 50, paddingRight: 25}}>
                        <ChooseDepartment departmentName={"Ås"}/>
                    </Grid.Column>
                </Grid.Row>
            </Grid>
        </div>
    );
  }
}

export default ContactPage;
