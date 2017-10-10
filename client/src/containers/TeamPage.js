import React, {Component} from 'react';
import TestimonialRow from '../components/TestimonialRow';
import TeamOverview from '../components/TeamOverview'
import {Grid} from 'semantic-ui-react';
import GradientBox from '../components/GradientBox';
import './TeamPage.css';

class TeamPage extends Component {
  render() {
    return (
        <div>
            <GradientBox>
                <Grid verticalAlign='middle' columns={2} className="gradient-box-grid">
                    <Grid.Row>
                        <Grid.Column>
                            <div className="team-header-text">
                                <h1 className="gradientBoxText">Vi får vektorprogrammet til å gå rundt!</h1>
                                <h3 className="gradientBoxText">Teamene har ansvar for alt fra rekruttering til drift av nettsiden, sponsorer og lignende. Alle organisasjonelle oppgaver tas hånd om av frivillige teammedlemmer</h3>
                            </div>
                        </Grid.Column>

                        <Grid.Column>
                        </Grid.Column>
                    </Grid.Row>
                </Grid>
            </GradientBox>
            <TestimonialRow/>
            <TeamOverview />
        </div>
    )
  }
}

export default TeamPage;
