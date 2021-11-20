import { Component, Input, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Game } from '@app/_models';
import { GameService } from '@app/_services';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-game-create',
  templateUrl: './game-edit.component.html',
})
export class GameEditComponent implements OnInit {
  @Input() game: Game = new Game();

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
      title: [this.game.title, Validators.required],
      description: [this.game.description],
      image_url: [this.game.image_url],
      genre: [this.game.genre],
      rating: [this.game.rating]
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
    this.gameService.update(this.game.id, this.form.value)
      .subscribe(
        res => {
          this.loading = false;
          this.activeModal.close('updated');
        },
        err => {
          this.loading = false;
        }
      );
  }

}
