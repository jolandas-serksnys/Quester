import { Component, Input, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Game, Map, Quest, Task } from '@app/_models';
import { GameService, MapService, QuestService, ToastService } from '@app/_services';
import { NgbActiveModal, NgbNav } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-game-manage',
  templateUrl: './game-manage.component.html',
})
export class GameManageComponent implements OnInit {
  @Input() game: Game = new Game;
  @ViewChild(NgbNav) nav: NgbNav;

  maps: Map[] = [];
  quests: Quest[] = [];
  tasks: Task[] = [];

  mapCreate: FormGroup;
  mapEdit: FormGroup;
  questCreate: FormGroup;
  questEdit: FormGroup;
  taskCreate: FormGroup;
  taskEdit: FormGroup;

  showMapEdit = false;
  showQuestManage = false;
  showQuestCreate = false;
  showQuestEdit = false;
  showTaskManage = false;
  showTaskCreate = false;
  showTaskEdit = false;

  selectedMapId: number = -1;
  selectedQuestId: number = -1;
  selectedTaskId: number = -1;

  loading = false;
  submitted = false;
  error = '';

  constructor(
    public toastService: ToastService,
    private formBuilder: FormBuilder,
    public activeModal: NgbActiveModal,
    private gameService: GameService,
    private mapService: MapService,
    private questsService: QuestService
    ) { }

  ngOnInit(): void {
    this.mapCreate = this.formBuilder.group({
      title: ['', Validators.required],
      description: [''],
      image_url: ['']
    });

    this.mapEdit = this.formBuilder.group({
      id: ['', Validators.required],
      title: ['', Validators.required],
      description: [''],
      image_url: ['']
    });

    this.questCreate = this.formBuilder.group({
      title: ['', Validators.required],
      description: [''],
      image_url: [''],
      map_coord_x: [''],
      map_coord_y: ['']
    });

    this.questEdit = this.formBuilder.group({
      id: ['', Validators.required],
      title: ['', Validators.required],
      description: [''],
      image_url: [''],
      map_coord_x: [''],
      map_coord_y: ['']
    });

    this.taskCreate = this.formBuilder.group({
      title: ['', Validators.required],
      description: [''],
      image_url: ['']
    });

    this.taskEdit = this.formBuilder.group({
      id: ['', Validators.required],
      title: ['', Validators.required],
      description: [''],
      image_url: ['']
    });

    this.loadMaps();
  }

  loadMaps() {
    this.mapService.getGameMaps(this.game.id).subscribe(res => {
      this.maps = res;
    }, err => {

    })
  }

  loadQuests() {
    this.quests = [];
    this.questsService.getGameMapQuests(this.game.id, this.selectedMapId).subscribe(res => {
      this.quests = res;
    }, err => {

    })
  }

  loadTasks() {
    this.tasks = this.quests.filter(q => q.id == this.selectedQuestId)[0].tasks;
  }

  get mcf() { return this.mapCreate.controls; }
  get mef() { return this.mapEdit.controls; }
  get qcf() { return this.questCreate.controls; }
  get qef() { return this.questEdit.controls; }
  get tcf() { return this.taskCreate.controls; }
  get tef() { return this.taskEdit.controls; }

  manageMapQuests(mapId) {
    this.selectedMapId = mapId;

    this.showQuestManage = true;
    this.showTaskCreate = false;
    this.showQuestEdit = false;
    this.showTaskManage = false;
    this.showTaskCreate = false;
    this.showTaskEdit = false;
    this.loadQuests();
    this.nav.select(3);
  }

  cancelManageMapQuests() {
    this.selectedMapId = -1;

    this.nav.select(2);
    this.showQuestManage = false;
    this.showTaskCreate = false;
    this.showQuestEdit = false;
    this.showTaskManage = false;
    this.showTaskCreate = false;
    this.showTaskEdit = false;
  }

  manageMapQuestTasks(questId) {
    this.selectedQuestId = questId;

    this.showTaskManage = true;
    this.showTaskCreate = false;
    this.showTaskEdit = false;
    this.loadTasks();
    this.nav.select(6);
  }

  cancelManageMapQuestTasks() {
    this.selectedQuestId = -1;

    this.nav.select(3);
    this.showTaskManage = false;
    this.showTaskCreate = false;
    this.showTaskEdit = false;
  }

  editMap(mapId) {
    let map = this.maps.filter(m => m.id == mapId)[0];

    if(!map)
      return;

    this.selectedMapId = mapId;

    this.showMapEdit = true;
    this.mapEdit = this.formBuilder.group({
      title: [map.title, Validators.required],
      description: [map.description],
      image_url: [map.image_url]
    });

    this.nav.select(3);
  }

  cancelEditMap() {
    this.selectedMapId = -1;

    this.mapEdit.reset();
    this.nav.select(2);
    this.showMapEdit = false;
  }

  createMap() {
    this.nav.select(1);
  }

  createQuest() {
    this.showQuestCreate = true;
    this.nav.select(4);
  }

  cancelCreateQuest() {
    this.nav.select(3);
    this.showQuestCreate = false;
  }

  editQuest(questId) {
    let quest = this.quests.filter(q => q.id == questId)[0];

    if(!quest)
      return;

    this.selectedQuestId = questId;

    this.showQuestEdit = true;
    this.questEdit = this.formBuilder.group({
      id: [quest.id, Validators.required],
      title: [quest.title, Validators.required],
      description: [quest.description],
      image_url: [quest.image_url],
      map_coord_x: [quest.map_coord_x],
      map_coord_y: [quest.map_coord_y]
    });

    this.nav.select(5);
  }

  cancelEditQuest() {
    this.taskEdit.reset();
    this.selectedQuestId = -1;

    this.nav.select(3);
    this.showQuestEdit = false;
  }

  createTask() {
    this.showTaskCreate = true;
    this.nav.select(7);
  }

  cancelCreateTask() {
    this.taskCreate.reset();
    this.nav.select(6);
    this.showTaskCreate = false;
  }

  editTask(taskId) {
    let task = this.tasks.filter(t => t.id == taskId)[0];

    if(!task)
      return;

    this.selectedTaskId = taskId;

    this.showTaskEdit = true;
    this.taskEdit = this.formBuilder.group({
      id: [task.id, Validators.required],
      title: [task.title, Validators.required],
      description: [task.description],
      image_url: [task.image_url]
    });

    this.nav.select(8);
  }

  cancelEditTask() {
    this.selectedTaskId = -1;

    this.nav.select(6);
    this.showTaskEdit = false;
  }

  onSubmitCreateMap() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.mapCreate.invalid) {
      return;
    }

    this.loading = true;
    this.mapService.create(this.game.id, this.mapCreate.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadMaps();
          this.toastService.show('A map has been added successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.nav.select(2);
          this.mapCreate.reset();
          this.submitted = false;
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to add a map', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

  onSubmitEditMap() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.mapEdit.invalid) {
      return;
    }

    this.loading = true;
    this.mapService.update(this.game.id, this.selectedMapId, this.mapEdit.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadMaps();
          this.toastService.show('The map has been updated successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.submitted = false;
          this.cancelEditMap();
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to updated the map', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

  onSubmitDeleteMap(mapId) {
    this.mapService.delete(this.game.id, mapId)
      .subscribe(
        res => {
          this.loadMaps();
          this.toastService.show('The map has been deleted successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });
        },
        err => {
          this.toastService.show('An error has occured when trying to delete the map', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
        }
      );

      this.showQuestManage = false;
      this.showQuestEdit = false;
      this.showQuestCreate = false;
      this.showTaskManage = false;
      this.showTaskCreate = false;
      this.showTaskEdit = false;
  }

  onSubmitCreateQuest() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.questCreate.invalid) {
      return;
    }

    this.loading = true;
    this.questsService.create(this.game.id, this.selectedMapId, this.questCreate.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadQuests();
          this.toastService.show('The quest has been added successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.submitted = false;
          this.cancelCreateQuest();
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to add the quest', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

  onSubmitEditQuest() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.questEdit.invalid) {
      return;
    }

    this.loading = true;
    this.questsService.update(this.game.id, this.selectedMapId, this.selectedQuestId, this.questEdit.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadQuests();
          this.toastService.show('The quest has been updated successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.submitted = false;
          this.cancelEditQuest();
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to update the quest', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

  onSubmitDeleteQuest(questId) {
    this.questsService.delete(this.game.id, this.selectedMapId, questId)
      .subscribe(
        res => {
          this.loadMaps();
          this.toastService.show('The quesk has been deleted successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.loadQuests();
        },
        err => {
          this.toastService.show('An error has occured when trying to delete the quesk', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
        }
      );

      this.showQuestEdit = false;
      this.showQuestCreate = false;
      this.showTaskManage = false;
      this.showTaskCreate = false;
      this.showTaskEdit = false;
  }

  onSubmitCreateTask() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.taskCreate.invalid) {
      return;
    }

    this.loading = true;
    this.questsService.createTask(this.game.id, this.selectedMapId, this.selectedQuestId, this.taskCreate.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadQuests();
          this.toastService.show('The task has been added successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.submitted = false;
          this.tasks.push(res);
          this.cancelCreateTask();
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to add the task', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

  onSubmitEditTask() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.taskEdit.invalid) {
      return;
    }

    this.loading = true;
    this.questsService.editTask(this.game.id, this.selectedMapId, this.selectedQuestId, this.selectedTaskId, this.taskEdit.value)
      .subscribe(
        res => {
          this.loading = false;
          this.loadQuests();
          this.toastService.show('The task has been updated successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.submitted = false;

          this.tasks = this.tasks.map(t => t.id == this.selectedTaskId ? res : t);
          this.cancelEditTask();
        },
        err => {
          this.loading = false;
          this.toastService.show('An error has occured when trying to update the task', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
          this.submitted = false;
        }
      );
  }

  onSubmitDeleteTask(taskId) {
    this.questsService.deleteTask(this.game.id, this.selectedMapId, this.selectedQuestId, taskId)
      .subscribe(
        res => {
          this.loadMaps();
          this.toastService.show('The task has been deleted successfully', {
            classname: 'bg-success text-light',
            delay: 2000 ,
            autohide: true
          });

          this.tasks = this.tasks.filter(t => t.id != taskId);
        },
        err => {
          this.toastService.show('An error has occured when trying to delete the task', {
            classname: 'bg-danger text-light',
            delay: 2000 ,
            autohide: true
          });
        }
      );

      this.showTaskCreate = false;
      this.showTaskEdit = false;
  }

}
