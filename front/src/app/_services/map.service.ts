import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Map } from '@app/_models';
import { environment } from '@environments/environment';

@Injectable({
  providedIn: 'root'
})
export class MapService {

  constructor(
    private http: HttpClient
  ) { }

  getGameMaps(gameId: number) {
    return this.http.get<Map[]>(`${environment.apiUrl}/games/${gameId}/maps`);
  }

  create(gameId, body) {
    return this.http.post<Map>(`${environment.apiUrl}/games/${gameId}/maps`, body);
  }

  update(gameId, mapId, body) {
    return this.http.put<Map>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}`, body);
  }

  delete(gameId, mapId) {
    return this.http.delete<Map>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}`);
  }
}
