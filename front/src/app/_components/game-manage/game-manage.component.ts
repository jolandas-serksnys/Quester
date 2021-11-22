import { Component, Input, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Game, Map } from '@app/_models';
import { GameService, MapService, ToastService } from '@app/_services';
import { NgbActiveModal, NgbNav } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-game-manage',
  templateUrl: './game-manage.component.html',
})
export class GameManageComponent implements OnInit {
  @Input() game: Game = new Game;
  @ViewChild(NgbNav) nav: NgbNav;

  maps: Map[] = [];

  mapCreate: FormGroup;
  mapEdit: FormGroup;
  mapEditId: number = -1;

  loading = false;
  submitted = false;
  error = '';

  constructor(
    public toastService: ToastService,
    private formBuilder: FormBuilder,
    public activeModal: NgbActiveModal,
    private gameService: GameService,
    private mapService: MapService
    ) { }

  ngOnInit(): void {
    this.mapCreate = this.formBuilder.group({
      title: ['', Validators.required],
      description: [''],
      image_url: ['']
    });

    this.mapEdit = this.formBuilder.group({
      id: ['', Validators.required],
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

  get mcf() { return this.mapCreate.controls; }
  get mef() { return this.mapEdit.controls; }

  editMap(mapId) {
    let map = this.maps.filter(m => m.id == mapId)[0];

    if(!map)
      return;

    this.mapEdit = this.formBuilder.group({
      title: [map.title, Validators.required],
      description: [map.description],
      image_url: [map.image_url]
    });

    this.mapEditId = map.id;
    this.nav.select(3);
  }

  cancelEditMap() {
    this.nav.select(2);
    this.mapEdit.reset();
    this.mapEditId = -1;
  }

  deleteMap(mapId) {
    this.mapService.delete(this.game.id, mapId)
      .subscribe(
        res => {
          this.loadMaps();
          this.toastService.show('The map has been deleted successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });
        },
        err => {
          this.toastService.show('An error has occured when trying to delete the map', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
        }
      );
  }

  onSubmitCreateMap() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.mapCreate.invalid) {
      return;
    }

    this.loading = true;
    this.mapService.create(this.game.id, this.mapCreate.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadMaps();
          this.toastService.show('A map has been added successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.nav.select(2);
          this.mapCreate.reset();
          this.submitted = false;
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to add a map', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

  onSubmitEditMap() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.mapEdit.invalid) {
      return;
    }

    this.loading = true;
    this.mapService.update(this.game.id, this.mapEditId, this.mapEdit.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadMaps();
          this.toastService.show('The map has been updated successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.submitted = false;
          this.cancelEditMap();
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to updated the map', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

}
