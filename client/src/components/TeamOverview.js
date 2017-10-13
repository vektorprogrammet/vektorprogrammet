import React, { Component } from 'react';
import { Icon, Header, Menu, Segment,  Button, Card, Image, List, Label, Tab } from 'semantic-ui-react';
import './TeamOverview.css';
import TeamCard from './TeamCard';
import {TeamApi} from '../api/TeamApi.js';

export default class TeamOverview extends Component {

    constructor(props) {
        super(props);
        this.state = {
            teams:[]
        };

    }

    async componentDidMount() {
        const allTeams = TeamApi.getAll();
        this.setState(
            {teams: await allTeams}
        );
    }

    render() {
        this.state.teams.forEach( (team) => {
            console.log(team);
        });
        const cardGroup = this.state.teams.map( (team) =>
                                                <TeamCard
                                                name={team.name}
                                                email={team.email}
                                                shortDesc={team.short_description}
                                                accept_application={team.accept_application}
                                                />
        );

        const trondContent = (
            <Card.Group className="teamCardGroup">
                {cardGroup}
            </Card.Group>
        );
        const panes = [
            { menuItem: 'Trondheim', render: () => <Tab.Pane className="tabPaneContent">{trondContent}</Tab.Pane> },
            { menuItem: 'Oslo', render: () => <Tab.Pane className="tabPaneContent">Oslo</Tab.Pane> },
            { menuItem: 'Bergen', render: () => <Tab.Pane className="tabPaneContent">Bergen</Tab.Pane> },
            { menuItem: 'Ås', render: () => <Tab.Pane className="tabPaneContent">Ås</Tab.Pane> },
            { menuItem: 'Tromsø', render: () => <Tab.Pane className="tabPaneContent">Tromsø</Tab.Pane> },
            { menuItem: 'Hovedstyret', render: () => <Tab.Pane className="tabPaneContent">Hovbedstyret</Tab.Pane>},
        ];

        return (
                <Segment>
                <Header as="h1" id="teamHeader">Team</Header>
                <Tab menu={{ id:"regionMenu", compact:true, stackable:true, attached:false, secondary: true, pointing: false }} panes={panes} />
                </Segment>
        );
    }

}
