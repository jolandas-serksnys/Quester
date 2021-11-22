import { Component, Input, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Game } from '@app/_models';
import { GameService, ToastService } from '@app/_services';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-game-delete',
  templateUrl: './game-delete.component.html',
})
export class GameDeleteComponent implements OnInit {
  @Input() game: Game = new Game();

  constructor(
    public toastService: ToastService,
    public activeModal: NgbActiveModal,
    private gameService: GameService,
    private router: Router
  ) { }

  ngOnInit(): void {
  }

  onSubmit() {
    this.gameService.delete(this.game.id).subscribe(res => {
      this.toastService.show('Game has been successfully deleted', {
        classname: 'bg-success text-light',
        delay: 2000 ,
        autohide: true
      });
      this.activeModal.dismiss();
      this.router.navigate(["/"]);
    }, err => {
      this.toastService.show('An error has occured when trying to delete the game', {
        classname: 'bg-danger text-light',
        delay: 2000 ,
        autohide: true
      });
      this.activeModal.dismiss();
    })
  }
}
