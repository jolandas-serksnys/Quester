import { Component, Input, OnInit } from '@angular/core';
import { Game } from '@app/_models';
import { GameService } from '@app/_services';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ModalGameComponent } from '../modal-game/modal-game.component';

@Component({
  selector: 'app-games-grid',
  templateUrl: './games-grid.component.html',
})
export class GamesGridComponent implements OnInit {
  @Input() games: Game[] = [];

  constructor(
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {}

  open(game: Game) {
    const modalRef = this.modalService.open(ModalGameComponent, { size: 'xl', centered: true });
    modalRef.result.then((result) => {
      //this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      //this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
    modalRef.componentInstance.game = game;
  }

  getUrlTitle(title) {
    return title.replace(/\s+/g, '-').toLowerCase();
  }
}
