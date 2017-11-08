import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import './HomePage.css';
import { Grid } from 'semantic-ui-react';
import { fetchDepartments } from '../actions/department';
import ContactDepartment from '../components/ContactDepartment';
import './ContactPage.css';

class ContactPage extends Component {
  componentDidMount() {
    if (!this.props.departments.length > 0) {
      this.props.fetchDepartments();
    }
  }

  render() {
    return (
      <div className="contact-page">
        <Grid>
          <Grid.Column width={16}>
            <div className="contact-main">
              <ContactDepartment departments={this.props.departments}/>
            </div>
          </Grid.Column>
        </Grid>
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
export default connect(mapStateToProps, mapDispatchToProps)(ContactPage);
