import { LocationStrategy } from '@angular/common';
import { Route } from '@angular/compiler/src/core';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { GameCreateComponent } from '@app/_components/game-create/game-create.component';
import { Game } from '@app/_models';
import { GameService } from '@app/_services';

import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-games',
  templateUrl: './games.component.html',
})
export class GamesComponent implements OnInit {
  games: Game[] = [];
  title: string = "Quester";
  isOwnedView: boolean = false;

  constructor(
    private gameService: GameService,
    private url: LocationStrategy,
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {
    if (this.url.path().split('/').includes('owned')) {
      this.title = "Owned games";
      this.isOwnedView = true;
      this.loadOwnedGames();
    } else {
      this.gameService.getAll().subscribe(r => {
        this.games = r;
      }, e => {

      })
    }
  }

  loadOwnedGames() {
    this.gameService.getOwned().subscribe(r => {
      this.games = r;
    }, e => {

    })
  }

  createGame() {
    const modalRef = this.modalService.open(GameCreateComponent, { centered: true, scrollable: true, size: 'lg' });
    modalRef.result.then((result) => {
      this.loadOwnedGames();
    }, (reason) => {
    })
    //modalRef.componentInstance.name = 'World';
  }
}
