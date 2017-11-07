import React, {Component} from 'react';
import {Header, Segment, Card, Tab, Loader, Dimmer} from 'semantic-ui-react';
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
                <Dimmer active={this.props.dimmerActive} inverted>
                    <Loader inverted/>
                </Dimmer>
                <Header as="h1" id="teamHeader">Oversikt</Header>
                <Tab menu={{
                    id: "regionMenu",
                    compact: false,
                    stackable: true,
                    attached: false,
                    secondary: true,
                    pointing: true
                }} panes={this.getPanes(this.props.departments)}/>
            </Segment>
        );
    }

}
