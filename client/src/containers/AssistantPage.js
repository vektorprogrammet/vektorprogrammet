import React from 'react';

import { Grid, Header } from 'semantic-ui-react';
import './AssistantPage.css';
import ApplicationFormContainer from './ApplicationFormContainer';
import NewsletterFormContainer from './NewsletterFormContainer';
import PageHeader from '../components/PageHeader';
import FeatureGroup from '../components/Feature/FeatureGroup';

import AssistantInfo from '../components/AssistantPage/AssistantInfo';
import TaskInfo from '../components/AssistantPage/TaskInfo';
import AdmissionProcessInfo from '../components/AssistantPage/AdmissionProcessInfo';

import { fetchDepartments } from '../actions/department';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { reduxForm, formValueSelector, change } from 'redux-form';


//TODO: test om vi har aktiv søkeperiode.
const AssistantPage = () => (
  <div className='assistant-page'>
    <PageHeader className="container">
      <h1>Assistenter</h1>
      <p>Vektorassistent er et frivillig verv der du reiser til en ungdomsskole én dag i uka for å hjelpe til som <span
        className="medium-bold">lærerassistent i matematikk</span>. En stilling som vektorassistent varer i 4 eller 8
        uker, og du kan selv velge hvilken ukedag som passer best for deg. </p>
    </PageHeader>

    <section className="large container"><FeatureGroup/></section>

    <div className="container">
      <section><AssistantInfo/></section>
      <section><TaskInfo/></section>
      <section><AdmissionProcessInfo/></section>

      <section>
        <Grid.Row columns={1}>
          <Grid.Column width={9} className="assistantApplicationForm centered">
            <div>
                <ApplicationFormContainer/>
            </div>
          </Grid.Column>
        </Grid.Row>
      </section>
    </div>
  </div>
);

const mapStateToProps = state => ({
    departments: state.departments.filter(d => d.active_admission),
});


export default AssistantPage;