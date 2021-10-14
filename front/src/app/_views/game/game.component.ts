import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Game } from '@app/_models';
import { GameService } from '@app/_services';

@Component({
  selector: 'app-game',
  templateUrl: './game.component.html',
})
export class GameComponent implements OnInit {
  game: Game;

  constructor(
    private route: ActivatedRoute,
    private gameService: GameService
    ) { }

  ngOnInit(): void {
    let id = <number><unknown>this.route.snapshot.paramMap.get('gameId');
    this.gameService.get(id).subscribe(r => {
      this.game = r;
    }, e => {

    })
  }

}
