import React, {Component} from 'react';
import {Header, Segment, Card, Tab} from 'semantic-ui-react';
import './TeamOverview.css';
import {TeamCard} from '../TeamCard';

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

        });

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
                }} panes={this.getPanes(this.props.departments)}/>
            </Segment>
        );
    }

}
