import { Component, Input, OnInit } from '@angular/core';
import { Map, Quest } from '@app/_models';
import { AuthenticationService, MapService } from '@app/_services';
import { QuestService } from '@app/_services/quest.service';

@Component({
  selector: 'app-map',
  templateUrl: './map.component.html',
})
export class MapComponent implements OnInit {
  @Input() gameId: number;

  maps: Map[] = [];
  quests: Quest[] = [];
  completedTasks: {user_id: number, task_id: number}[] = [];
  selectedMapIndex: number = 0;
  selectedQuestIndex: number = 0;

  loadingQuestData = false;
  isLoggedIn = false;

  constructor(
    private mapService: MapService,
    private questService: QuestService,
    private authenticationService: AuthenticationService
  ) {
    this.isLoggedIn = this.authenticationService.currentUserValue !== null;
   }

  ngOnInit(): void {
    this.mapService.getGameMaps(this.gameId).subscribe(r => {
      this.maps = r;
      this.selectMap(this.selectedMapIndex);
    }, e => {

    });
  }

  selectMap(i) {
    if(!this.maps[i])
      return;

    this.selectedMapIndex = i;

    this.quests = [];
    this.questService.getGameMapQuests(this.maps[this.selectedMapIndex].game_id, this.maps[this.selectedMapIndex].id).subscribe(r => {
      this.quests = r;
      this.getComleted();
    })
  }

  selectQuest(i) {
    if(!this.quests[i] && i != -1)
      return;

    this.getComleted();
    this.selectedQuestIndex = i;
  }

  getComleted() {
    if(this.isLoggedIn && this.selectedMapIndex)
      this.questService.getTasksCompleted(this.maps[this.selectedMapIndex].game_id, this.maps[this.selectedMapIndex].id, this.quests[this.selectedQuestIndex].id).subscribe(res => {
        this.completedTasks = res;
      }, err => {

      })
  }

  toggleCompleted(taskId) {
    this.questService.toggleCompleted(this.maps[this.selectedMapIndex].game_id, this.maps[this.selectedMapIndex], this.quests[this.selectedQuestIndex], taskId).subscribe(res => {
      this.getComleted();
    }, err => {

    })
  }

  isSelected(id) {
    return this.completedTasks.some(t => t.task_id == id);
  }
}
