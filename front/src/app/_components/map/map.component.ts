import { Component, Input, OnInit } from '@angular/core';
import { Map, Quest } from '@app/_models';
import { MapService } from '@app/_services';
import { QuestService } from '@app/_services/quest.service';

@Component({
  selector: 'app-map',
  templateUrl: './map.component.html',
})
export class MapComponent implements OnInit {
  @Input() gameId: number;

  maps: Map[] = [];
  quests: Quest[] = [];
  selectedMapIndex: number = 0;
  selectedQuestIndex: number = 0;

  constructor(
    private mapService: MapService,
    private questService: QuestService
  ) { }

  ngOnInit(): void {
    this.mapService.getGameMaps(this.gameId).subscribe(r => {
      this.maps = r;
      this.selectMap(this.selectedMapIndex);
    }, e => {

    })
  }

  selectMap(i) {
    if(!this.maps[i])
      return;

    this.selectedMapIndex = i;
    
    this.quests = [];
    this.questService.getGameMapQuests(this.maps[this.selectedMapIndex].game_id, this.maps[this.selectedMapIndex].id).subscribe(r => {
      this.quests = r;
    })
  }

  selectQuest(i) {
    if(!this.quests[i])
      return;

    this.selectedQuestIndex = i;
  }

}
