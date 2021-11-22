import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { User } from '@app/_models';
import { AuthenticationService, ToastService } from '@app/_services';
import { Observable } from 'rxjs';
import { first } from 'rxjs/operators';

@Component({
  selector: 'app-account',
  templateUrl: './account.component.html',
})
export class AccountComponent implements OnInit {
  user: User = new User();
  userObservable: Observable<User>;
  accountForm: FormGroup;
  loading = false;
  submitted = false;
  error = '';

  constructor(
    public toastService: ToastService,
    private formBuilder: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
    private authenticationService: AuthenticationService
  ) {
    this.user = authenticationService.currentUserValue;
    this.userObservable = authenticationService.currentUser;
  }

  ngOnInit(): void {
    this.accountForm = this.formBuilder.group({
      email: [{value: this.user.email, disabled: true}, Validators.required],
      name: [this.user.name, Validators.required]
    });
  }

  get f() { return this.accountForm.controls; }

  onSubmit() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.accountForm.invalid) {
      return;
    }

    this.loading = true;
    this.authenticationService.update(this.accountForm.value)
    .pipe(first())
    .subscribe({
      next: () => {
        this.toastService.show('Your account has been updated successfully', {
          classname: 'bg-success text-light',
          delay: 2000 ,
          autohide: true
        });
        this.loading = false;
        this.submitted = false;
      },
      error: error => {
        this.loading = false;
        this.toastService.show('An error has occured when trying to update your account details', {
          classname: 'bg-danger text-light',
          delay: 2000 ,
          autohide: true
        });
        this.error = error;
        this.loading = false;
      }
    });
  }
}
