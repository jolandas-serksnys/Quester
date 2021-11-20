import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { GameEditComponent } from '@app/_components/game-edit/game-edit.component';
import { Game, Map, Quest } from '@app/_models';
import { AuthenticationService, GameService, MapService } from '@app/_services';
import { QuestService } from '@app/_services/quest.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-game',
  templateUrl: './game.component.html',
})
export class GameComponent implements OnInit {
  gameId: number;
  game: Game = new Game();
  maps: Map[] = [];
  quests: Quest[] = [];
  selectedMapIndex: number = 0;
  isOwnerView: boolean = false;
  loading = true;

  constructor(
    private route: ActivatedRoute,
    private gameService: GameService,
    private mapService: MapService,
    private authenticationService: AuthenticationService,
    private modalService: NgbModal,
    private router: Router
    ) {
    }

  ngOnInit(): void {
    this.gameId = <number><unknown>this.route.snapshot.paramMap.get('gameId');
    this.loadGameData();
  }

  loadGameData() {
    this.loading = true;
    this.gameService.get(this.gameId).subscribe(r => {
      this.isOwnerView = this.authenticationService.currentUserValue != undefined && this.authenticationService.currentUserValue.id == r.owner_id;
      this.game = r;
      this.loading = false;

      this.mapService.getGameMaps(r.id).subscribe(r2 => {
        this.maps = r2;
      }, e2 => {

      })
    }, e => {
      this.router.navigate(['/']);
    })
  }

  editGame() {
    const modalRef = this.modalService.open(GameEditComponent, { centered: true, scrollable: true, size: 'lg' });
    modalRef.componentInstance.game = this.game;
    modalRef.result.then((result) => {
      this.loadGameData();
    }, (reason) => {
    })
  }
}
