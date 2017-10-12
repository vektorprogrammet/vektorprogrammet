import React, { Component } from 'react';
import { Header, Menu, Segment,  Button, Card, Image, List, Label, Tab } from 'semantic-ui-react';
import './TeamOverview.css';

export default class TeamOverview extends Component {
    render() {
        const trondContent = (
            <Card.Group className="teamCardGroup">
                <Card>
                    <Image className="teamImage" src="https://vektorprogrammet.no/images/team_images/Skolekoord_size_bw.jpg" />
                    <Card.Content>
                        <Card.Header>Skolekoordinering!</Card.Header>
                        <Card.Meta>Har opptak</Card.Meta>
                        <Card.Description className="teamCardDesc">Vi er knutepunktet mellom skoler og assistenter.</Card.Description>
                    </Card.Content>
                </Card>
                <Card>
                    <Image className="teamImage" src="https://vektorprogrammet.no/images/team_images/img_sponsor.jpg" />
                    <Card.Content>
                        <Card.Header>Samarbeidskoordinatorer!</Card.Header>
                        <Card.Meta>Har opptak</Card.Meta>
                        <Card.Description className="teamCardDesc">Vi er knutepunktet mellom Vektor og næringslivet.</Card.Description>
                    </Card.Content>
                </Card>
                <Card>
                    <Image className="teamImage" src="https://vektorprogrammet.no/images/team_images/itteam.jpg" />
                    <Card.Content>
                        <Card.Header>IT!</Card.Header>
                        <Card.Meta>Har opptak</Card.Meta>
                        <Card.Description className="teamCardDesc">Vi drifter og utvikler Vektorprogrammet.no.</Card.Description>
                    </Card.Content>
                </Card>
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
