import { Base } from ".";

export class User extends Base {
    access_token: string;
    token_type: string;
    expires_in: number;
    name: string;
    email: string;
    email_verified_at?: string;
}