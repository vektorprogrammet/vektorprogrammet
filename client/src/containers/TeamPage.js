import React, {Component} from 'react';
import TestimonialRow from '../components/TestimonialRow';
import TeamOverviewContainer from '../components/TeamOverview/TeamOverviewContainer'
import GradientBox from '../components/GradientBox/GradientBox';

class TeamPage extends Component {
    render() {
        return (
            <div>
                <GradientBox/>
                <TestimonialRow/>
                <TeamOverviewContainer />
            </div>
        )
    }
}

export default TeamPage;
