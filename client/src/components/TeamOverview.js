import React, { Component } from 'react';
import { Menu, Segment,  Button, Card, Image, List, Label, Tab } from 'semantic-ui-react'
import './TeamOverview.css'

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
            { menuItem: 'Trondheim', render: () => <Tab.Pane>{trondContent}</Tab.Pane> },
            { menuItem: 'Oslo', render: () => <Tab.Pane>Oslo</Tab.Pane> },
            { menuItem: 'Bergen', render: () => <Tab.Pane>Bergen</Tab.Pane> },
            { menuItem: 'Ås', render: () => <Tab.Pane>Ås</Tab.Pane> },
            { menuItem: 'Tromsø', render: () => <Tab.Pane>Tromsø</Tab.Pane> },
        ];
        return <Tab menu={{ fluid: true, widths: 5 }} panes={panes} />
    }

}