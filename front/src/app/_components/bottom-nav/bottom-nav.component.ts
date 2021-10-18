import { Component, OnInit } from '@angular/core';

interface BottomNavItem {
  text: string;
  icon: string;
  link: string;
}

@Component({
  selector: 'app-bottom-nav',
  templateUrl: './bottom-nav.component.html',
})
export class BottomNavComponent implements OnInit {
  items: BottomNavItem[] = [];

  constructor() { 
    this.items.push({
      text: 'Quester',
      icon: 'house-fill',
      link: '/games'
    }, {
      text: 'Search',
      icon: 'search',
      link: '/search'
    }, {
      text: 'My Games',
      icon: 'controller',
      link: '/my/games'
    }, {
      text: 'Account',
      icon: 'person-circle',
      link: '/my/account'
    })
  }

  ngOnInit(): void {
  }

}
