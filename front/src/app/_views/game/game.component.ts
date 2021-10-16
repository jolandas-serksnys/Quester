import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Game, Map } from '@app/_models';
import { GameService, MapService } from '@app/_services';

@Component({
  selector: 'app-game',
  templateUrl: './game.component.html',
})
export class GameComponent implements OnInit {
  game: Game = new Game();
  maps: Map[] = [];
  selectedMapIndex: number = 0;

  constructor(
    private route: ActivatedRoute,
    private gameService: GameService,
    private mapService: MapService
    ) { }

  ngOnInit(): void {
    let id = <number><unknown>this.route.snapshot.paramMap.get('gameId');
    this.gameService.get(id).subscribe(r => {
      this.game = r;

      this.mapService.getGameMaps(r.id).subscribe(r2 => {
        this.maps = r2;
      }, e2 => {

      })
    }, e => {

    })
  }

  selectMap(i) {
    this.selectedMapIndex = i;
  }

}
