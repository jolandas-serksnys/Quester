import { Base, User } from ".";

export class Game extends Base {
    title: string;
    description: string;
    image_url: string;
    genre: string;
    rating: string;
    owner_id: number;
    owner?: User;
    maps_count?: number;
}
