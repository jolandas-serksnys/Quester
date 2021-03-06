import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Quest, Task } from '@app/_models';
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

  create(gameId, mapId, body) {
    return this.http.post<Quest[]>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests`, body);
  }

  update(gameId, mapId, questId, body) {
    return this.http.put<Quest[]>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests/${questId}`, body);
  }

  delete(gameId, mapId, questId) {
    return this.http.delete<Quest>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests/${questId}`);
  }

  // TASKS

  createTask(gameId, mapId, questId, body) {
    return this.http.post<Task>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests/${questId}/tasks`, body);
  }

  editTask(gameId, mapId, questId, taskId, body) {
    return this.http.put<Task>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests/${questId}/tasks/${taskId}`, body);
  }

  deleteTask(gameId, mapId, questId, taskId) {
    return this.http.delete<Task>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests/${questId}/tasks/${taskId}`);
  }

  getTasksCompleted(gameId, mapId, questId) {
    return this.http.get<{user_id: number, task_id: number}[]>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests/${questId}/tasks/completed`);
  }

  toggleCompleted(gameId, mapId, questId, taskId) {
    return this.http.post<Task>(`${environment.apiUrl}/games/${gameId}/maps/${mapId}/quests/${questId}/tasks/${taskId}/toggle`, {});
  }
}
