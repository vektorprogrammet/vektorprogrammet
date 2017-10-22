import React, {Component} from 'react';
import {DepartmentApi} from '../../api/DepartmentApi';
import TeamOverview from './TeamOverview';

export default class TeamOverviewContainer extends Component {
    state = {departments: [] };

    async componentDidMount() {
        this.setState({ departments: await DepartmentApi.getAll() })
    }

    render() {
        return <TeamOverview departments={this.state.departments}/>
    }
}