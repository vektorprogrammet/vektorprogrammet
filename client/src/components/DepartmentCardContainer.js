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
            <Grid.Column style={{paddingLeft: 45, paddingRight: 20}}>
                <ChooseDepartment departmentName={item.school} logo={item.logo}/>
            </Grid.Column>
            );

        return (
            <div className="card-container">
                <Grid stackable>
                    <Grid.Row columns={overview.length} style={{paddingLeft: 70, paddingRight: 70}}>
                        {departmentName}
                    </Grid.Row>
                </Grid>
            </div>
        );
    }
}

export default DepartmentCardContainer;