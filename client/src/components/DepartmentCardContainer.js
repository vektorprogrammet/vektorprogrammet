import React, {Component} from 'react';
import {Grid} from 'semantic-ui-react';
import ChooseDepartment from "./ChooseDepartment";
import './DepartmentCardContainer.css';


class DepartmentCardContainer extends Component {

    render() {
        const overview =  [{school:"NTNU", logo: "https://via.placeholder.com/300x200"},
            {school:"HiST", logo: "https://via.placeholder.com/300x200"},
            {school:"NMBU", logo: "https://via.placeholder.com/300x200"},
            {school:"Ås", logo: "https://via.placeholder.com/300x200"}];
        const departmentName = overview.map(item =>
            <Grid.Column style={{paddingLeft: 50, paddingRight: 25}}>
                <ChooseDepartment departmentName={item.school} logo={item.logo}/>
            </Grid.Column>
            );

        return (
            <div className="card-container">
                <Grid stackable>
                    <Grid.Row columns={4} style={{paddingLeft: 100, paddingRight: 100, margin: '0 10px'}}>
                        {departmentName}
                    </Grid.Row>
                </Grid>
            </div>
        );
    }
}

export default DepartmentCardContainer;