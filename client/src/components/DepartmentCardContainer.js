import React, {Component} from 'react';
import {Grid} from 'semantic-ui-react';
import ChooseDepartment from "./ChooseDepartment";

class DepartmentCardContainer extends Component {
    render() {
        return (
            <Grid stackable>
                <Grid.Row style={{paddingLeft: 100, paddingRight: 100}}>
                    <Grid.Column style={{paddingLeft: 50, paddingRight: 25}}>
                        <ChooseDepartment departmentName="NTNU"/>
                    </Grid.Column>
                </Grid.Row>
            </Grid>
        );
    }
}

export default DepartmentCardContainer;