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
  game: Game = new Game();
  maps: Map[] = [];
  quests: Quest[] = [];
  selectedMapIndex: number = 0;

  constructor(
    private route: ActivatedRoute,
    private gameService: GameService,
    private mapService: MapService,
    private questService: QuestService
    ) { }

  ngOnInit(): void {
    let id = <number><unknown>this.route.snapshot.paramMap.get('gameId');
    this.gameService.get(id).subscribe(r => {
      this.game = r;

      this.mapService.getGameMaps(r.id).subscribe(r2 => {
        this.maps = r2;
        this.selectMap(this.selectedMapIndex)
      }, e2 => {

      })
    }, e => {

    })
  }

  selectMap(i) {
    this.selectedMapIndex = i;
    
    this.questService.getGameMapQuests(this.game.id, this.maps[this.selectedMapIndex].id).subscribe(r => {
      this.quests = r;
    })
  }

}
