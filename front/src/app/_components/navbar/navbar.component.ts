import { Component, OnInit } from '@angular/core';
import { User } from '@app/_models';
import { AuthenticationService } from '@app/_services';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
})
export class NavbarComponent implements OnInit {
  user: User = new User();
  userObservable: Observable<User>;

  constructor(
    private authenticationService: AuthenticationService
  ) {
    this.userObservable = authenticationService.currentUser;
  }

  ngOnInit(): void {
    this.user = this.authenticationService.currentUserValue;
  }

  logout():void {
    this.authenticationService.logout();
  }

}
