import React from 'react';
import {Icon, Card, Image, Responsive} from 'semantic-ui-react';

export const TeamCard = ({imagePath, name, email, shortDesc, accept_application}) => {
    return (
        <Card className="team-card">
            <Responsive minWidth={Responsive.onlyTablet.minWidth}>
                <Image className="teamImage" src={imagePath}/>
            </Responsive>
            <Card.Content>
                <Card.Header>{name}</Card.Header>
                <Card.Meta className="teamCardMeta"><a href={"mailto:" + email}>{email}</a></Card.Meta>
                <Card.Description className="teamCardDesc">{shortDesc}</Card.Description>
            </Card.Content>
            { accept_application &&
            <Card.Content extra className="teamCardExtra">
                <a>
                    <Icon color="green" name='star'/>
                    Har opptak!
                </a>
            </Card.Content>
            }
        </Card>
    );
};
