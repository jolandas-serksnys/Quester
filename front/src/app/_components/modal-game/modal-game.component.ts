import { Component, Input, OnInit } from '@angular/core';
import { Game } from '@app/_models';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-modal-game',
  templateUrl: './modal-game.component.html',
})
export class ModalGameComponent implements OnInit {
  @Input() game: Game = new Game();
  sidebarView: string = 'game';

  constructor(
    public activeModal: NgbActiveModal
  ) { }

  ngOnInit(): void {
  }

  getUrlTitle(title) {
    return title.replace(/\s+/g, '-').toLowerCase();
  }

  setSidebarView(view) {
    this.sidebarView = view;
  }
}
