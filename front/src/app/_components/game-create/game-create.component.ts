import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { GameService } from '@app/_services';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-game-create',
  templateUrl: './game-create.component.html',
})
export class GameCreateComponent implements OnInit {
  form: FormGroup;
  loading = false;
  submitted = false;
  error = '';
  ratings = [
    { value: "", text: "Unset" },
    { value: "Pegi7", text: "Pegi7" },
    { value: "Pegi14", text: "Pegi14" },
    { value: "Pegi18", text: "Pegi18" },
    { value: "Mature", text: "Mature" }
  ]

  constructor(
    private formBuilder: FormBuilder,
    public activeModal: NgbActiveModal,
    private gameService: GameService
  ) { }

  ngOnInit() {
    this.form = this.formBuilder.group({
      title: ['', Validators.required],
      description: [''],
      image_url: [''],
      genre: [''],
      rating: ['']
    });
  }

  // convenience getter for easy access to form fields
  get f() { return this.form.controls; }

  onSubmit() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.form.invalid) {
      return;
    }

    this.loading = true;
    this.gameService.create(this.form.value)
      .subscribe(
        res => {
          this.loading = false;
          this.activeModal.close('addded');
        },
        err => {
          this.loading = false;
        }
      );
  }

}
