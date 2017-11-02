import React, {Component} from 'react';
import TestimonialRow from '../components/TestimonialRow';
import TeamOverviewContainer from '../components/TeamOverview/TeamOverviewContainer'
import GradientBox from '../components/GradientBox/GradientBox';
import PageHeader from '../components/PageHeader';
import "./TeamPage.css";

class TeamPage extends Component {
    render() {
        return (
            <div>
                <div className="container">
                    <PageHeader>
                        <h1>Team</h1>
                        <p>Teamene har ansvar for alt fra rekruttering til drift av
                            nettsiden, sponsorer og lignende. Alle organisasjonelle oppgaver tas hånd om av
                            frivillige teammedlemmer. <span className="font-weight-normal">Vi får vektorprogrammet til å gå rundt! </span>
                        </p>
                    </PageHeader>
                    {/*<GradientBox/>*/}
                </div>
                <div className="team-page-overview">
                    <TeamOverviewContainer />
                </div>
            </div>
        )
    }
}

export default TeamPage;
