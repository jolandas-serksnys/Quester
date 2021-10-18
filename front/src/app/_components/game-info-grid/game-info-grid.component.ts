import { Component, Input, OnInit } from '@angular/core';
import { Game } from '@app/_models';

@Component({
  selector: 'app-game-info-grid',
  templateUrl: './game-info-grid.component.html',
})
export class GameInfoGridComponent implements OnInit {
  @Input() game: Game = new Game();

  constructor() { }

  ngOnInit(): void {
  }

}
