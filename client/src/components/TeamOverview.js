import React, { Component } from 'react';
import { Icon, Header, Menu, Segment,  Button, Card, Image, List, Label, Tab } from 'semantic-ui-react';
import './TeamOverview.css';

export default class TeamOverview extends Component {
    render() {
        const trondContent = (
            <Card.Group className="teamCardGroup">
                <Card>
                    <Image className="teamImage" src="https://vektorprogrammet.no/images/team_images/Skolekoord_size_bw.jpg" />
                    <Card.Content>
                        <Card.Header>Skolekoordinering!</Card.Header>
                        <Card.Meta className="teamCardMeta">skolekoordinering.ntnu@vektorprogrammet.no</Card.Meta>
                        <Card.Description className="teamCardDesc">Skolekoordinering fungerer som et bindeledd mellom skolene og vektorassistentene gjennom semesteret.	</Card.Description>
                    </Card.Content>
                </Card>
                <Card>
                    <Image className="teamImage" src="https://vektorprogrammet.no/images/team_images/img_sponsor.jpg" />
                    <Card.Content>
                        <Card.Header>Samarbeidskoordinatorer!</Card.Header>
                        <Card.Meta className="teamCardMeta">sponsor.ntnu@vektorprogrammet.no</Card.Meta>
                        <Card.Description className="teamCardDesc">Vektorprogrammets bindeledd til næringslivet, samarbeidspartnere og sponsorer.	</Card.Description>
                    </Card.Content>
                </Card>
                <Card>
                    <Image className="teamImage" src="https://vektorprogrammet.no/images/team_images/itteam.jpg" />
                    <Card.Content>
                        <Card.Header>IT!</Card.Header>
                        <Card.Meta className="teamCardMeta">it@vektorprogrammet.no</Card.Meta>
                        <Card.Description className="teamCardDesc">Vi drifter og utvikler Vektorprogrammet.no.</Card.Description>
                    </Card.Content>
                    <Card.Content extra className="teamCardExtra">
                        <a>
                            <Icon color="green" name='star' />
                            Har opptak!
                        </a>
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
