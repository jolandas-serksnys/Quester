import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Game } from '@app/_models';
import { environment } from '@environments/environment';

@Injectable({
  providedIn: 'root'
})
export class GameService {

  constructor(
    private http: HttpClient
  ) { }

  getAll() {
    return this.http.get<Game[]>(`${environment.apiUrl}/games`);
  }

  getOwned() {
    return this.http.get<Game[]>(`${environment.apiUrl}/games/owned`);
  }

  create(body) {
    return this.http.post<Game>(`${environment.apiUrl}/games`, body);
  }

  get(id) {
    return this.http.get<Game>(`${environment.apiUrl}/games/${id}`);
  }

  update(id, body) {
    return this.http.put<Game>(`${environment.apiUrl}/games/${id}`, body);
  }

  delete(id) {
    return this.http.delete<Game>(`${environment.apiUrl}/games/${id}`);
  }
}
