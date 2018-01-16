import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { reduxForm, formValueSelector } from 'redux-form';

import { getInactiveDepartments } from '../selectors/department';

import { postNewsletter } from '../actions/newsletter';
import NewsletterForm from '../components/NewsletterForm/NewsletterForm';
import NewsletterFormSubmitted from '../components/NewsletterForm/NewsletterFormSubmitted';


class NewsletterFormContainer extends Component {
  handleSubmit = this.props.handleSubmit(values => {
    this.props.postNewsletter(values)
  });

  render() {
    return (
      <NewsletterForm
        departments={this.props.departments}
        onSubmit={this.handleSubmit}
      />
    );

  }
}

const form = 'newsletter';
const selector = formValueSelector(form);

NewsletterFormContainer = reduxForm({
  form,
  enableReinitialize: true
})(NewsletterFormContainer);

const mapStateToProps = state => ({
  newsletter: state.newsletter,
  departments: getInactiveDepartments(state),
  initialValues: {
    department: getInactiveDepartments(state)[0]
  },
  selectedDepartment: selector(state, 'department'),
});


const mapDispatchToProps = dispatch => bindActionCreators({
  postNewsletter
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(NewsletterFormContainer);
