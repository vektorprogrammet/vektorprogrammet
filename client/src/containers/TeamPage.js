import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { fetchDepartments } from '../actions/department';

import TeamOverview from '../components/TeamOverview/TeamOverview';
import PageHeader from '../components/PageHeader';
import './TeamPage.css';

class TeamPage extends Component {
  componentDidMount() {
    if (!this.props.departments.length > 0) {
      this.props.fetchDepartments();
    }
  }

  render() {
    return (
      <div>
        <div className="container">
          <PageHeader>
            <h1>Team</h1>
            <p>Teamene har ansvar for alt fra rekruttering til drift av
              nettsiden, sponsorer og lignende. Alle organisasjonelle oppgaver tas hånd om av
              frivillige teammedlemmer. <span className="medium-bold">Vi får vektorprogrammet til å gå rundt! </span>
            </p>
          </PageHeader>
          {/*<GradientBox/>*/}
        </div>
        <div className="team-page-overview">
          <TeamOverview departments={this.props.departments}/>
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => ({
  departments: state.departments,
});

const mapDispatchToProps = dispatch => bindActionCreators({
  fetchDepartments,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(TeamPage);
