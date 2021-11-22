import { Component, Input, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Game, Map } from '@app/_models';
import { GameService, MapService } from '@app/_services';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-game-manage',
  templateUrl: './game-manage.component.html',
})
export class GameManageComponent implements OnInit {
  @Input() game: Game = new Game;

  maps: Map[] = [];

  mapForm: FormGroup;

  loading = false;
  submitted = false;
  error = '';

  constructor(
    private formBuilder: FormBuilder,
    public activeModal: NgbActiveModal,
    private gameService: GameService,
    private mapService: MapService
    ) { }

  ngOnInit(): void {
    this.mapForm = this.formBuilder.group({
      title: ['', Validators.required],
      description: [''],
      image_url: ['']
    });

    this.loadMaps();
  }

  loadMaps() {
    this.mapService.getGameMaps(this.game.id).subscribe(res => {
      this.maps = res;
    }, err => {

    })
  }

  get mf() { return this.mapForm.controls; }

  onSubmitCreateMap() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.mapForm.invalid) {
      return;
    }

    this.loading = true;
    this.mapService.create(this.game.id, this.mapForm.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadMaps();
        },
        err => {
          this.loading = false;
        }
      );
  }

}
