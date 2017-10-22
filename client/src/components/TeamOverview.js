import React, {Component} from 'react';
import {Icon, Header, Menu, Segment, Button, Card, Image, List, Label, Tab} from 'semantic-ui-react';
import './TeamOverview.css';
import TeamCard from './TeamCard';
import {DepartmentApi} from '../api/DepartmentApi.js';

export default class TeamOverview extends Component {

    getPanes(departments) {
        const panes = [];
        departments.forEach(function (department) {
            const teams = department.teams;
            const teamCards = teams.map((team) =>
                <TeamCard
                    key={team.name}
                    name={team.name}
                    email={team.email}
                    shortDesc={team.short_description}
                    accept_application={team.accept_application}
                />
            );

            const cardGroup = (
                <Card.Group className="teamCardGroup">
                    {teamCards}
                </Card.Group>
            );

            const pane = {
                menuItem: department.city,
                render: () =>
                    <Tab.Pane className="tabPaneContent">
                        {cardGroup}
                    </Tab.Pane>
            };

            panes.push(pane);

        }.bind(this));

        return panes;
    }

    render() {
        return (
            <Segment>
                <Header as="h1" id="teamHeader">Team</Header>
                <Tab menu={{
                    id: "regionMenu",
                    compact: true,
                    stackable: true,
                    attached: false,
                    secondary: true,
                    pointing: false
                }} panes={this.getPanes()}/>
            </Segment>
        );
    }

}
