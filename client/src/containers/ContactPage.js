import React from 'react';
import { connect } from 'react-redux';
import './HomePage.css';
import ContactDepartment from '../components/ContactDepartment';
import PageHeader from '../components/PageHeader';
import './ContactPage.css';

const ContactPage = ({departments}) => (
  <div className="contact-page large container">
    <PageHeader>
      <h1>Kontakt oss</h1>
    </PageHeader>
    <ContactDepartment departments={this.props.departments}/>
  </div>
);

const mapStateToProps = state => ({
  departments: state.departments,
});
export default connect(mapStateToProps)(ContactPage);
