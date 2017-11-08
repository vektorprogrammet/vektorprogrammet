import React, { Component } from 'react';
import { List, Button, Grid, Image, Responsive } from 'semantic-ui-react';
import './ContactDepartment.css';
import ContactUsPopUp from '../components/ContactUsPopUp';

class ContactDepartment extends Component {
  constructor(props) {
    super(props);
    this.state = {
      showModal: false,
    };
  }

  handleModal = () => {
    this.setState(prevState => ({
      showModal: !prevState.showModal,
    }));
  };

  render() {
    const departments = this.props.departments.map((item, index) =>
      <Grid.Row className={'contact-listSchool'} key={index}>
        <Grid.Column mobile={16} tablet={8} computer={8} widescreen={8}>
          <List>
            <List.Item><h2>{item.short_name}</h2></List.Item>
            <List.Item><p className={'contact-text'}>{item.name}</p></List.Item>
            <List.Item>
              <List.Icon name='mail' className={'contact-department-icon'}/>
              <List.Content verticalAlign={'middle'}>
                <p className={'font-weight-normal'}>
                  <a href={'mailto:' + item.email}>{item.email}</a>
                </p>
              </List.Content>
            </List.Item>
            <List.Item>
              <List.Icon name='marker' className={'contact-department-icon'}/>
              <List.Content verticalAlign={'middle'}>
                <p>{item.address}</p>
              </List.Content>
            </List.Item>
            <List.Item><Button primary onClick={this.handleModal}>Kontakt oss!</Button></List.Item>
            <br/><br/>
          </List>
        </Grid.Column>
        <Responsive as={Grid.Column} width={8} minWidth={Responsive.onlyTablet.minWidth}>
          <Image src={'http://via.placeholder.com/300x300/'}/>
        </Responsive>
      </Grid.Row>,
    );
    return (
      <div className="contact-department">
        <ContactUsPopUp show={this.state.showModal} onClose={this.handleModal}/>
        <Grid>
          {departments}
        </Grid>
      </div>
    );
  }
}

export default ContactDepartment;
