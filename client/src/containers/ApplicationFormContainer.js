import React, { Component } from 'react';

import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { fetchDepartments } from '../actions/department';

import ApplicationForm from '../components/ApplicationForm/ApplicationForm';

class ApplicationFormContainer extends Component {

  constructor(props) {
    super(props);
    this.state = {
      department: {},
      fieldOfStudyId: '',
      firstName: '',
      lastName: '',
      phone: '',
      email: '',
    };
  }

  componentDidMount() {
    if (this.props.departments.length === 0) {
      this.props.fetchDepartments();
    }
  }

  handleChange = (event) => {
    const name = event.target.name;
    let value = event.target.value;

    if (name === 'department') {
      const department = this.props.departments.find(d => d.id === parseInt(value, 10));
      value = department || {};
    }

    this.setState({
      [name]: value
    });
  };

  render() {
    if (this.props.departments.length === 0) {
      return false;
    }
    return (
      <ApplicationForm
        application={this.state}
        departments={this.props.departments}
        onChange={this.handleChange}
        onSubmit={() => {console.log("Submitted!", this.state)}}
      />
    );
  }
}

const mapStateToProps = state => ({
  departments: state.departments.filter(d => d.active_admission)
});

const mapDispatchToProps = dispatch => bindActionCreators({
  fetchDepartments,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(ApplicationFormContainer);
