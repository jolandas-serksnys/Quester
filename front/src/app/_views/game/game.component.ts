import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Game, Map, Quest } from '@app/_models';
import { GameService, MapService } from '@app/_services';
import { QuestService } from '@app/_services/quest.service';

@Component({
  selector: 'app-game',
  templateUrl: './game.component.html',
})
export class GameComponent implements OnInit {
  gameId: number;
  game: Game = new Game();
  maps: Map[] = [];
  quests: Quest[] = [];
  selectedMapIndex: number = 0;

  constructor(
    private route: ActivatedRoute,
    private gameService: GameService,
    private mapService: MapService
    ) { }

  ngOnInit(): void {
    this.gameId = <number><unknown>this.route.snapshot.paramMap.get('gameId');
    this.gameService.get(this.gameId).subscribe(r => {
      this.game = r;

      this.mapService.getGameMaps(r.id).subscribe(r2 => {
        this.maps = r2;
      }, e2 => {

      })
    }, e => {

    })
  }
}
