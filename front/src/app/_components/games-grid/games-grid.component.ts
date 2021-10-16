import { Component, Input, OnInit } from '@angular/core';
import { Game } from '@app/_models';
import { GameService } from '@app/_services';

@Component({
  selector: 'app-games-grid',
  templateUrl: './games-grid.component.html',
})
export class GamesGridComponent implements OnInit {
  @Input() games: Game[] = [];

  constructor() { }

  ngOnInit(): void {}

  getUrlTitle(title) {
    return title.replace(/\s+/g, '-').toLowerCase();
  }

}
