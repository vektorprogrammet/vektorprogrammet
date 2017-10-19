import React, {Component} from 'react';
import {Icon, Header, Menu, Segment, Button, Card, Image, List, Label, Tab} from 'semantic-ui-react';
import './TeamOverview.css';
import TeamCard from './TeamCard';
import {TeamApi} from '../api/TeamApi.js';
import {DepartmentApi} from '../api/DepartmentApi.js';

export default class TeamOverview extends Component {

    constructor(props) {
        super(props);
        this.state = {
            teams: new Map(),
            departments: [],
            cardGroup: new Map(),
            menuItems: []
        };

        this.getCard = this.getCard.bind(this);

    }

    componentDidMount() {
        const newTeams = new Map();
        const newCardGroup = new Map();
        DepartmentApi.getAll().then(json => {
            this.setState({departments: json})
        }).then(() => {
            const cls = this;
            this.state.departments.forEach(function (dep) {
                DepartmentApi.getTeams(dep.id).then(json => {
                    newTeams.set(dep.id, json);
                    newCardGroup.set(dep.id, cls.getCard(dep.id))
                })
            });
            this.setState(
                {teams: newTeams}
            );
            this.setState(
                {cardGroup: newCardGroup}
            );

            const menuItems = this.state.departments.map((dep) => {
                const item = {
                    menuItem: dep.city,
                    render: () => <Tab.Pane className="tabPaneContent">
                        <Card.Group className="teamCardGroup">
                            {this.state.cardGroup.get(dep.id)}
                        </Card.Group>
                    </Tab.Pane>
                };
                return item;
            });

            this.setState(
                {menuItems: menuItems}
            )
        })
    }

    getCard(department) {
        const teams = this.state.teams.get(department);
        const cardGroup = teams.map((team) =>
            <TeamCard key={team.name}
                      name={team.name}
                      email={team.email}
                      shortDesc={team.short_description}
                      accept_application={team.accept_application}
            />
        );
        return cardGroup;
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
                }} panes={this.state.menuItems}/>
            </Segment>
        );
    }

}
