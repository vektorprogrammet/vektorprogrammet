import React, {Component} from 'react';
import {Image, Card, Icon, Grid, Responsive} from 'semantic-ui-react';
import './ChooseDepartment.css';

class ChooseDepartment extends Component {

    handleClick = () => {
        alert("Hello");
    };

    render() {
        return (
            <div className="choose-department">
                <Grid centered stackable>
                    <Card fluid color="grey" onClick={this.handleClick} style={{margin: '.875em .5em', display: 'flex'
                        , float: 'none', width: 290, maxWidth: '100%', flexWrap: 'wrap'}}>
                        <Responsive minWidth={Responsive.onlyTablet.minWidth}>
                            <Image className="departmentLogo" src={this.props.logo}/>
                        </Responsive>
                        <Card.Content>
                            <Card.Header>
                                {this.props.departmentName}
                            </Card.Header>
                            <Card.Meta>
                            </Card.Meta>
                            <Card.Description>
                                Ta kontakt med {this.props.departmentName}
                            </Card.Description>
                        </Card.Content>
                        <Card.Content extra>
                            <a>
                                <Icon name='mail' />
                                CLICK ME M8
                            </a>
                        </Card.Content>
                    </Card>
                </Grid>
            </div>
        );
    }
}

export default ChooseDepartment;