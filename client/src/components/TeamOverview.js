import React, { Component } from 'react';
import { Icon, Header, Menu, Segment,  Button, Card, Image, List, Label, Tab } from 'semantic-ui-react';
import './TeamOverview.css';
import TeamCard from './TeamCard';

export default class TeamOverview extends Component {
    render() {
        const trondContent = (
            <Card.Group className="teamCardGroup">
                <TeamCard
                    imagePath="https://vektorprogrammet.no/images/team_images/Skolekoord_size_bw.jpg"
                    name="Skolekoordinering"
                    email="skolekoordinering.ntnu@vektorprogrammet.no"
                    shortDesc="Skolekoordinering fungerer som et bindeledd mellom skolene og vektorassistentene gjennom semesteret."
                    accept_application={false}
                    />

                <TeamCard
                    imagePath="https://vektorprogrammet.no/images/team_images/img_sponsor.jpg"
                    name="Samarbeidskoordinatorer"
                    email="sponsor.ntnu@vektorprogrammet.no"
                    shortDesc="Vektorprogrammets bindeledd til næringslivet, samarbeidspartnere og sponsorer."
                    accept_application={false}
                    />

                <TeamCard
                    imagePath="https://vektorprogrammet.no/images/team_images/itteam.jpg"
                    name="IT"
                    email="it@vektorprogrammet.no"
                    shortDesc="Vi drifter og utvikler Vektorprogrammet.no."
                    accept_application={true}
                    />
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
