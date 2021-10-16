import { Component, OnInit } from '@angular/core';
import { Game } from '@app/_models';
import { GameService } from '@app/_services';

@Component({
  selector: 'app-games',
  templateUrl: './games.component.html',
})
export class GamesComponent implements OnInit {
  games: Game[] = [];

  constructor(
    private gameService: GameService
  ) { }

  ngOnInit(): void {
    this.gameService.getAll().subscribe(r => {
      this.games = r;
    }, e => {

    })
  }
}
