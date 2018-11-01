describe('Log in to the page manually', () => {

  it('404 when visiting / while not logged in', () => {
    cy.visit('localhost:8080/');
    cy.contains('404');
  })

  it('Login renders password and username text and inputs', () => {
    cy.visit('localhost:8080/login');
    cy.contains('Brukernavn / e-post');
    cy.get('#usernameInput');
    cy.contains('Passord');
    cy.get('#passwordInput');
  });

  it('Corretly logging in should put you in "min-side"', () => {
    cy.visit('localhost:8080/login');
    cy.get('#usernameInput')
      .type('superadmin');
    cy.get('#passwordInput')
      .type('1234');
    cy.get('.btn')
      .click();
    cy.url().should('include', 'min-side');
  })

  it('Incorretly logging in should make you stay in login', () => {
    cy.visit('localhost:8080/login');
    cy.get('#usernameInput')
      .type('tull');
    cy.get('#passwordInput')
      .type('ball');
    cy.get('.btn')
      .click();
    cy.url().should('include', 'login');
  })
})

describe('Log in to the page with request', () => {
  it('Logging in via a POST request instead of manually', () => {
    cy.request({
      method: 'POST',
      url: 'localhost:8000/api/account/login',
      body: 'username=superadmin&password=1234',
      // This sets the header to Content-Type: application/x-www-form-urlencoded which is required
      form: true,
    });
  })

  it('Corretly logged in as superadmin should allow you to gain acces to kontrollpanel/staging', () => {
    cy.request({
      method: 'POST',
      url: 'localhost:8000/api/account/login',
      body: 'username=superadmin&password=1234',
      form: true,
    });
    cy.wait(1000);
    cy.visit('localhost:8080/kontrollpanel/staging');
    cy.contains('Staging server');
  })
});
