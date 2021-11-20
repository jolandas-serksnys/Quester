import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { AdminGuard, AuthGuard } from './_helpers';
import { GameComponent } from './_views/game/game.component';
import { GamesComponent } from './_views/games/games.component';
import { SignInComponent } from './_views/sign-in/sign-in.component';
import { SignUpComponent } from './_views/sign-up/sign-up.component';

const routes: Routes = [
  { path: 'games', component: GamesComponent },
  { path: 'games/owned', component: GamesComponent, canActivate: [AdminGuard] },
  { path: 'sign-in', component: SignInComponent },
  { path: 'sign-up', component: SignUpComponent },

  { path: 'g/:gameId', component: GameComponent },
  { path: 'g/:gameId/:gameTitle', component: GameComponent },

  // otherwise redirect to home
  { path: '**', redirectTo: 'games' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
