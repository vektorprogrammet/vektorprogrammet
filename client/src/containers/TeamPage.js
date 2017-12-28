import React from 'react';

import {connect} from 'react-redux';

import TeamOverview from '../components/TeamOverview/TeamOverview';
import PageHeader from '../components/PageHeader';
import {getSortedDepartments} from '../selectors/department';
import './TeamPage.css';

const TeamPage = ({departments}) => (
  <div>
    <div className="container">
      <PageHeader>
        <h1>Team</h1>
        <p>Teamene har ansvar for alt fra rekruttering til drift av
          nettsiden, sponsorer og lignende. Alle organisasjonelle oppgaver tas hånd om av
          frivillige teammedlemmer. <span
            className="medium-bold">Vi får vektorprogrammet til å gå rundt! </span>
        </p>
      </PageHeader>
    </div>
    <div className="team-page-overview">
      <TeamOverview departments={departments}/>
    </div>
  </div>
);

const mapStateToProps = state => ({
  departments: getSortedDepartments(state)
});

export default connect(mapStateToProps)(TeamPage);
