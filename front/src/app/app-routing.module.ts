import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { HomeComponent } from './home/home.component';
import { LoginComponent } from './login/login.component';
import { AuthGuard } from './_helpers';
import { GameComponent } from './_views/game/game.component';

const routes: Routes = [
  { path: 'g/:gameId', component: GameComponent, canActivate: [AuthGuard] },
  { path: 'g/:gameId/:gameTitle', component: GameComponent, canActivate: [AuthGuard] },

  { path: '', component: HomeComponent, canActivate: [AuthGuard] },
  { path: 'login', component: LoginComponent },

  // otherwise redirect to home
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
