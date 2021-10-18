import { Component, Input, OnInit } from '@angular/core';
import { Game, Map } from '@app/_models';
import { MapService } from '@app/_services';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-modal-game',
  templateUrl: './modal-game.component.html',
})
export class ModalGameComponent implements OnInit {
  @Input() game: Game = new Game();
  maps: Map[] = [];
  sidebarView: string = 'game';

  constructor(
    public activeModal: NgbActiveModal,
    private mapService: MapService
  ) { }

  ngOnInit(): void {
    this.mapService.getGameMaps(this.game.id).subscribe(r => {
      this.maps = r;
      //this.selectMap(this.selectedMapIndex)
    }, e => {

    })
  }

  getUrlTitle(title) {
    return title.replace(/\s+/g, '-').toLowerCase();
  }

  setSidebarView(view) {
    this.sidebarView = view;
  }
}
