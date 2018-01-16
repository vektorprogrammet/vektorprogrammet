import React, { Component } from 'react';

import { connect } from 'react-redux';
import { Grid } from 'semantic-ui-react';
import './AssistantPage.css';
import ApplicationFormContainer from './ApplicationFormContainer';
import NewsletterFormContainer from './NewsletterFormContainer';
import PageHeader from '../components/PageHeader';
import FeatureGroup from '../components/Feature/FeatureGroup';

import { getActiveDepartments, getInactiveDepartments } from '../selectors/department';

import AssistantInfo from '../components/AssistantPage/AssistantInfo';
import TaskInfo from '../components/AssistantPage/TaskInfo';
import AdmissionProcessInfo from '../components/AssistantPage/AdmissionProcessInfo';

const AssistantPage = ({activeDepartments, inactiveDepartments}) => {
  const applicationForm = (
    <div className={'assistant-page-application-form centered'}>
      <p>
        {activeDepartments.map((department, i) => (
          <span key={i}>{department.short_name}{i + 1 < activeDepartments.length ? ', ' : ' '}</span>
        ))}
        har opptak nå!
      </p>
      <ApplicationFormContainer/>
    </div>
  );

  return (
    <div className='assistant-page'>
      <PageHeader className="container">
        <h1>Assistenter</h1>
        <p>Vektorassistent er et frivillig verv der du reiser til en ungdomsskole én dag i uka for å hjelpe til
          som <span
            className="medium-bold">lærerassistent i matematikk</span>. En stilling som vektorassistent varer i 4
          eller 8
          uker, og du kan selv velge hvilken ukedag som passer best for deg. </p>
      </PageHeader>

      <section className="large container"><FeatureGroup/></section>

      <div className="container">
        <section><AssistantInfo/></section>
        <section><TaskInfo/></section>
        <section><AdmissionProcessInfo/></section>

        <section>
          {activeDepartments.length > 0 && applicationForm}
        </section>
      </div>
    </div>
  );
};

const mapStateToProps = state => ({
  departments: state.departments,
  activeDepartments: getActiveDepartments(state),
  inactiveDepartments: getInactiveDepartments(state),
});

export default connect(mapStateToProps)(AssistantPage);
