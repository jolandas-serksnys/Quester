import React, { Component } from "react";
import AuthService from "../_services/auth.service";

export default class Profile extends Component {
  constructor(props) {
    super(props);

    this.state = {
      currentUser: AuthService.getCurrentUser()
    };
  }

  render() {
    const { currentUser } = this.state;

    return (
      <div className="container">
        <header className="jumbotron">
          <h3>
            <strong>{currentUser.name}</strong> Profile
          </h3>
        </header>
        <p>
          <strong>Token:</strong>{" "}
          {currentUser.access_token.substring(0, 20)} ...{" "}
          {currentUser.access_token.substr(currentUser.access_token.length - 20)}
        </p>
        <p>
          <strong>Id:</strong>{" "}
          {currentUser.user.id}
        </p>
        <p>
          <strong>Email:</strong>{" "}
          {currentUser.user.email}
        </p>
        <strong>Authorities:</strong>
        <ul>
          {currentUser.roles &&
            currentUser.roles.map((role, index) => <li key={index}>{role}</li>)}
        </ul>
      </div>
    );
  }
}