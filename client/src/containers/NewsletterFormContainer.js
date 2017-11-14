import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { reduxForm, formValueSelector, change } from 'redux-form';

import { fetchDepartments } from '../actions/department';
import { postNewsletter } from '../actions/newsletter';
import NewsletterForm from '../components/NewsletterForm/NewsletterForm';
import NewsletterFormSubmitted from '../components/NewsletterForm/NewsletterFormSubmitted';


class NewsletterFormContainer extends Component {

    componentDidMount() {
        if (this.props.departments.length === 0) {
            this.props.fetchDepartments();
        }
    }

    handleSubmit = this.props.handleSubmit(values => {
        this.props.postNewsletter(values)
    });

    render(){


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
    enableReinitialize : true
})(NewsletterFormContainer);

const mapStateToProps = state => ({
    newsletter: state.newsletter,
    departments: state.departments.filter(d => d.active_admission),
    initialValues: {
        department: state.departments.filter(d => d.active_admission)[0]
    },
    selectedDepartment: selector(state, 'department'),
});


const mapDispatchToProps = dispatch => bindActionCreators({
    fetchDepartments,
    postNewsletter
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(NewsletterFormContainer);
