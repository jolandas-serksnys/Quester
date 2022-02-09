export interface Game {
    id: number;
    title: string;
    genre: string;
    description: string;
    image_url: string;
    owner_id: number;
    rating?: string;
    created_at: string;
    updated_at: string;

    owner?: {}
    maps_count?: number;
}