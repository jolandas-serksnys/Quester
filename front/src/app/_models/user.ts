export class User {
    access_token: string;
    token_type: string;
    expires_in: number;
    user: {
        id: number;
        name: string;
        email: string;
        email_verified_at?: string;
        created_at: string;
        updated_at: string;
    }
}