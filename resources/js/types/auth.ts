export type User = {
    id: number;
    fist_name: string;
    last_name: string;
    display_name: string;
    fullName: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
    // Maps permission strings to a boolean, e.g., { edit_users: true }
    can: Record<string, boolean>;
    
    // Maps role strings to their lowercase string value, e.g., { super_admin: 'super_admin' }
    roles: Record<string, string>;
};

export type Passkey = {
    id: number;
    name: string;
    authenticator: string | null;
    created_at_diff: string;
    last_used_at_diff: string | null;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
