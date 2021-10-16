import { Base, Task } from ".";

export class Quest extends Base {
    title: string;
    description: string;
    image_url: string;
    map_coord_y: number;
    map_coord_x: number;
    map_id: number;
    tasks: Task[];
}
