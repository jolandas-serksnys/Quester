import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Quest } from '@app/_models';
import { environment } from '@environments/environment';

@Injectable({
  providedIn: 'root'
})
export class QuestService {

  constructor(
    private http: HttpClient
  ) { }

  getGameMapQuests(gameId: number, mapId: number) {
    return this.http.get<Quest[]>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests`);
  }
}
