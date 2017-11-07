import React, {Component} from 'react';
import {DepartmentApi} from '../../api/DepartmentApi';
import TeamOverview from './TeamOverview';

export default class TeamOverviewContainer extends Component {
    state = {departments: [], dimmerActive: true};

    async componentDidMount() {
        this.setState({ departments: await DepartmentApi.getAll() });
        this.setState({ dimmerActive: false});
    }

    render() {
        return <TeamOverview departments={this.state.departments} dimmerActive={this.state.dimmerActive}/>
    }
}